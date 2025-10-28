<?php

namespace App\Http\Controllers;

use App\Models\Artifact;
use App\Models\Certificate;
use App\Models\DiamondEvaluation;
use App\Models\ArtifactEvaluation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display certificates for an artifact
     */
    public function index(Artifact $artifact)
    {
        $certificates = $artifact->certificates()
            ->with('generatedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Dashboard/Certificates/Index', [
            'artifact' => $artifact->load('client'),
            'certificates' => $certificates,
        ]);
    }

    /**
     * Generate certificate from evaluation data
     */
    public function generate(Request $request, Artifact $artifact)
    {
        // التحقق من وجود تقييم للقطعة
        $evaluation = null;
        $evaluationData = [];

        if ($artifact->type === 'Colorless Diamonds') {
            $evaluation = $artifact->diamondEvaluations()
                ->where('status', 'completed')
                ->latest()
                ->first();
                
            if ($evaluation) {
                $evaluationData = $this->mapDiamondEvaluationData($evaluation, $artifact);
            }
        } else {
            // للأنواع الأخرى (Colored Gemstones & Jewellery)
            $evaluation = $artifact->evaluations()
                ->where('is_final', true)
                ->latest()
                ->first();
                
            if ($evaluation) {
                $evaluationData = $this->mapGeneralEvaluationData($evaluation, $artifact);
            }
        }

        // إضافة معلومات التقييم للـ log للتشخيص
        if ($evaluation) {
            \Log::info('Evaluation details:', [
                'evaluation_id' => $evaluation->id,
                'evaluation_data' => $evaluation->toArray(),
                'artifact_type' => $artifact->type,
                'artifact_weight' => $artifact->weight,
                'artifact_weight_unit' => $artifact->weight_unit
            ]);
        }

        if (!$evaluation) {
            return back()->withErrors(['error' => 'No completed evaluation found for this artifact.']);
        }

        \Log::info('Creating certificate with evaluation data:', [
            'artifact_id' => $artifact->id,
            'evaluation_data' => $evaluationData,
            'evaluation_id' => $evaluation->id,
            'artifact_type' => $artifact->type,
            'evaluation_type' => $artifact->type === 'Colorless Diamonds' ? 'diamond' : 'general'
        ]);

        // إنشاء الشهادة
        $certificate = Certificate::create([
            'artifact_id' => $artifact->id,
            'generated_by' => auth()->id(),
            'received_date' => $artifact->created_at->toDateString(),
            'report_date' => now()->toDateString(),
            'test_date' => $evaluation->created_at->toDateString(),
            ...$evaluationData,
            'status' => 'draft',
        ]);

        // تحديث حالة القطعة إلى certified
        $artifact->update([
            'status' => 'certified',
            'assigned_to' => auth()->id(),
        ]);

        \Log::info('Certificate created successfully:', $certificate->toArray());
        \Log::info('Artifact status updated to certified:', [
            'artifact_id' => $artifact->id,
            'artifact_code' => $artifact->artifact_code
        ]);

        return redirect()->route('certificates.show', $certificate)
            ->with('success', 'Certificate generated successfully!');
    }

    /**
     * Show certificate details
     */
    public function show(Certificate $certificate)
    {
        $certificateData = $certificate->load(['artifact.client', 'generatedBy']);
        
        // Add QR code URL to the response
        $certificateData->qr_code_url = $certificate->qr_code_url;
        
        return Inertia::render('Dashboard/Certificates/Show', [
            'certificate' => $certificateData,
        ]);
    }

    /**
     * Issue certificate (mark as final)
     */
    public function issue(Certificate $certificate)
    {
        $certificate->markAsIssued();

        return back()->with('success', 'Certificate issued successfully!');
    }

    /**
     * Regenerate QR Code for certificate
     */
    public function regenerateQR(Certificate $certificate)
    {
        $certificate->generateQRCode();

        return back()->with('success', 'QR Code regenerated successfully!');
    }

    /**
     * Generate PDF certificate
     */
    public function generatePDF(Certificate $certificate)
    {
        $data = [
            'certificate' => $certificate->load(['artifact.client', 'generatedBy']),
        ];

        $pdf = Pdf::loadView('certificates.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'enable_javascript' => false,
                'debugKeepTemp' => false,
                'chroot' => public_path(),
            ]);
        
        return $pdf->download("certificate-{$certificate->certificate_number}.pdf");
    }

    /**
     * Display all certified artifacts including uploaded certificates
     */
    public function certified()
    {
        $artifacts = Artifact::with(['client', 'latestCertificate', 'testRequest'])
            ->where('status', 'certified')
            ->whereHas('latestCertificate', function($query) {
                $query->whereIn('status', ['issued', 'uploaded']);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return Inertia::render('Dashboard/Certificates/Certified', [
            'artifacts' => $artifacts,
        ]);
    }

    /**
     * Download QR Code as PNG for a specific artifact
     */
    public function downloadQR(Artifact $artifact)
    {
        // Check if artifact has an evaluation
        $evaluation = null;
        if ($artifact->type === 'Colorless Diamonds') {
            $evaluation = $artifact->diamondEvaluations()
                ->where('status', 'completed')
                ->latest()
                ->first();
        } else {
            $evaluation = $artifact->evaluations()
                ->where('is_final', true)
                ->latest()
                ->first();
        }

        if (!$evaluation) {
            return back()->withErrors(['error' => 'No completed evaluation found for this artifact.']);
        }

        // Generate unique token for this artifact's QR code
        $token = \Str::random(32);
        
        // Create QR codes directory if it doesn't exist
        $qrDir = public_path('storage/qr-codes');
        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0755, true);
        }

        // Check if artifact has uploaded certificate to determine URL
        $uploadedCertificate = Certificate::where('artifact_id', $artifact->id)
            ->where('status', 'uploaded')
            ->latest()
            ->first();
            
        // Decide target URL: direct file if uploaded, otherwise verification page
        if ($uploadedCertificate && $uploadedCertificate->uploaded_certificate_path) {
            $targetUrl = url('/certificate-file/' . $uploadedCertificate->uploaded_certificate_path);
        } else {
            $targetUrl = url('/verify-artifact/' . $token);
        }
        
        // Generate SVG first (no ImageMagick dependency)
        $qrCodeSvg = \QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($targetUrl);

        // Create an HTML page that will automatically convert SVG to PNG and download
        $htmlContent = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>QR Code Generator</title>
            <style>
                body { margin: 0; padding: 20px; text-align: center; font-family: Arial, sans-serif; background: #f5f5f5; }
                .container { max-width: 400px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .qr-code { margin: 20px 0; }
                .artifact-info { font-weight: bold; margin-bottom: 20px; font-size: 18px; color: #333; }
                .loading { margin-top: 20px; color: #666; }
                .success { color: #28a745; font-weight: bold; }
                canvas { border: 2px solid #ddd; border-radius: 8px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="artifact-info">IDG Laboratory<br/>QR Code for: ' . $artifact->artifact_code . '<br/>' . 
                ($uploadedCertificate ? '<span style="color: #28a745;">📄 Direct Certificate Access</span>' : '<span style="color: #007bff;">🔐 Official Authentication</span>') . '</div>
                <div class="qr-code" id="qr-container">' . $qrCodeSvg . '</div>
                <div class="loading" id="status">جاري إنشاء ملف PNG...</div>
                <canvas id="canvas" style="display:none;"></canvas>
            </div>

            <script>
                window.onload = function() {
                    // Get the SVG element
                    const svg = document.querySelector(\'svg\');
                    const canvas = document.getElementById(\'canvas\');
                    const ctx = canvas.getContext(\'2d\');
                    const status = document.getElementById(\'status\');
                    
                    // Set canvas size
                    canvas.width = 350;
                    canvas.height = 350;
                    
                    // Create image from SVG
                    const svgData = new XMLSerializer().serializeToString(svg);
                    const svgBlob = new Blob([svgData], {type: \'image/svg+xml;charset=utf-8\'});
                    const url = URL.createObjectURL(svgBlob);
                    
                    const img = new Image();
                    img.onload = function() {
                        // Clear canvas with white background
                        ctx.fillStyle = \'#ffffff\';
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        
                        // Draw image centered
                        const size = 300;
                        const x = (canvas.width - size) / 2;
                        const y = (canvas.height - size) / 2;
                        ctx.drawImage(img, x, y, size, size);
                        
                        // Convert to PNG and download
                        canvas.toBlob(function(blob) {
                            const link = document.createElement(\'a\');
                            link.download = \'QR-' . $artifact->artifact_code . '.png\';
                            link.href = URL.createObjectURL(blob);
                            
                            // Update status
                            status.innerHTML = \'<span class="success">✅ تم إنشاء الملف بنجاح!</span>\';
                            
                            // Auto download
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            
                            // Cleanup
                            URL.revokeObjectURL(url);
                            URL.revokeObjectURL(link.href);
                            
                            setTimeout(() => {
                                window.close();
                            }, 1000);
                        }, \'image/png\');
                    };
                    
                    img.onerror = function() {
                        status.innerHTML = \'خطأ في إنشاء الصورة\';
                    };
                    
                    img.src = url;
                };
            </script>
        </body>
        </html>';

        // Store the token in the artifact for later verification
        $artifact->update([
            'qr_token' => $token,
            'qr_code_path' => 'qr-codes/artifact-' . $artifact->artifact_code . '.png',
        ]);

        // Return the HTML page that will handle PNG conversion and download
        return response($htmlContent)
            ->header('Content-Type', 'text/html');
    }

    /**
     * Upload certificate PDF with QR code
     */
    public function uploadCertificate(Request $request, Artifact $artifact)
    {
        try {
            // Increase memory limit for large file processing
            ini_set('memory_limit', '512M');
            set_time_limit(600); // 10 minutes
            
            $request->validate([
                'certificate_file' => 'required|file|mimes:pdf|max:102400', // 100MB max
            ]);

            // Check if artifact has QR token, create one if missing
            if (!$artifact->qr_token) {
                $artifact->update(['qr_token' => \Str::random(32)]);
                $artifact->refresh(); // Reload the artifact with the new token
            }

            // Find existing certificate record (get the latest one)
            $certificate = Certificate::where('artifact_id', $artifact->id)->latest()->first();
            
            // If replacing an existing uploaded certificate, delete the old file first
            if ($certificate && $certificate->status === 'uploaded' && $certificate->uploaded_certificate_path) {
                $oldFilePath = public_path('storage/' . $certificate->uploaded_certificate_path);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Store the uploaded file
            $certificateFile = $request->file('certificate_file');
            $fileName = 'certificate-' . $artifact->artifact_code . '-' . time() . '.pdf';
            $filePath = $certificateFile->storeAs('certificates', $fileName, 'public');

            if (!$certificate) {
                // Create a basic certificate record
                $certificate = Certificate::create([
                    'artifact_id' => $artifact->id,
                    'generated_by' => auth()->id(),
                    'received_date' => $artifact->created_at->toDateString(),
                    'report_date' => now()->toDateString(),
                    'test_date' => now()->toDateString(),
                    'status' => 'uploaded',
                    'qr_code_token' => $artifact->qr_token,
                    'identification' => strtoupper($artifact->type),
                    'weight' => $artifact->weight ?? 0,
                    'conclusion' => 'Uploaded Certificate',
                ]);
            } else {
                // Update existing certificate to ensure it has required fields
                if (!$certificate->certificate_number) {
                    $certificate->certificate_number = $certificate->generateCertificateNumber();
                }
                if (!$certificate->qr_code_token) {
                    $certificate->qr_code_token = $artifact->qr_token;
                }
                $certificate->save();
            }

            // Update certificate with uploaded file
            $certificate->update([
                'uploaded_certificate_path' => $filePath,
                'uploaded_at' => now(),
                'status' => 'uploaded',
            ]);

            // Update artifact status to certified if not already (without updating timestamps)
            if ($artifact->status !== 'certified') {
                $artifact->timestamps = false;
                $artifact->update(['status' => 'certified']);
                $artifact->timestamps = true;
            }

            $message = $certificate->wasRecentlyCreated ? 'Certificate uploaded successfully!' : 'Certificate replaced successfully!';
            
            \Log::info('Certificate uploaded successfully', [
                'artifact_code' => $artifact->artifact_code,
                'certificate_id' => $certificate->id,
                'file_path' => $filePath,
                'was_replacement' => !$certificate->wasRecentlyCreated
            ]);

            return redirect()->back()->with('success', $message);
            
        } catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
            return back()->withErrors(['error' => 'File is too large. Maximum allowed size is 100MB.']);
        } catch (\Exception $e) {
            \Log::error('Certificate upload error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete uploaded certificate
     */
    public function deleteCertificate(Artifact $artifact)
    {
        try {
            // Find the uploaded certificate for this artifact
            $certificate = Certificate::where('artifact_id', $artifact->id)
                ->where('status', 'uploaded')
                ->whereNotNull('uploaded_certificate_path')
                ->latest()
                ->first();

            if (!$certificate) {
                return back()->withErrors(['error' => 'No uploaded certificate found for this artifact.']);
            }

            // Delete the physical file
            if ($certificate->uploaded_certificate_path) {
                $filePath = public_path('storage/' . $certificate->uploaded_certificate_path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Update certificate status to draft and remove uploaded file info
            $certificate->update([
                'uploaded_certificate_path' => null,
                'uploaded_at' => null,
                'status' => 'draft',
            ]);

            // Update artifact status back to evaluated if no other certificates exist
            $hasOtherCertificates = Certificate::where('artifact_id', $artifact->id)
                ->where('status', '!=', 'draft')
                ->exists();

            if (!$hasOtherCertificates) {
                $artifact->timestamps = false;
                $artifact->update(['status' => 'evaluated']);
                $artifact->timestamps = true;
            }

            \Log::info('Certificate deleted successfully', [
                'artifact_code' => $artifact->artifact_code,
                'certificate_id' => $certificate->id
            ]);

            return redirect()->back()->with('success', 'Certificate deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Certificate deletion error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete certificate: ' . $e->getMessage()]);
        }
    }


    /**
     * Map diamond evaluation data to certificate format
     */
    private function mapDiamondEvaluationData($evaluation, $artifact)
    {
        return [
            'identification' => 'DIAMOND',
            'shape_cut' => $evaluation->shape ?? 'Round',
            'weight' => $evaluation->weight ? floatval($evaluation->weight) : 0.00,
            'color' => $evaluation->colour ?? 'Colorless',
            'dimensions' => $evaluation->diameter ? "{$evaluation->diameter} mm" : null,
            'transparency' => 'Transparent',
            'phenomena' => null,
            'species' => 'Diamond',
            'variety' => 'Diamond',
            'group' => 'Diamond',
            'origin_opinion' => 'Not requested',
            'conclusion' => $evaluation->stone_type ?? 'Natural',
            'comments' => $evaluation->comments,
            'test_method' => 'SOPOI',
            'report_number' => $evaluation->report_number,
        ];
    }

    /**
     * Map general evaluation data to certificate format
     */
    private function mapGeneralEvaluationData($evaluation, $artifact)
    {
        // استخدام البيانات الفعلية من التقييم
        $weight = 0.00;
        if ($evaluation->weight) {
            $weight = floatval($evaluation->weight);
        } elseif ($artifact->weight) {
            $weight = floatval($artifact->weight);
        }

        // معالجة خاصة للمجوهرات
        if ($artifact->type === 'Jewellery') {
            return [
                'identification' => 'JEWELLERY',
                'shape_cut' => $evaluation->product_type ?? 'Ring',
                'weight' => $weight,
                'color' => $evaluation->metal_type ?? 'Various',
                'dimensions' => $evaluation->dimensions ?? '0.00 x 0.00 x 0.00 mm',
                'transparency' => 'Opaque',
                'phenomena' => $evaluation->stamp ?? '-',
                'species' => 'Jewellery',
                'variety' => $evaluation->product_type ?? 'Ring',
                'group' => 'Jewellery',
                'origin_opinion' => 'Not requested',
                'conclusion' => $evaluation->result ?? 'Pass',
                'comments' => $evaluation->comments ?? 'Jewellery evaluation completed',
                'test_method' => 'SOPOI',
                'report_number' => null,
            ];
        }

        // للأنواع الأخرى (Colored Gemstones) - استخدام البيانات الفعلية
        $extractedData = $this->extractEvaluationData($evaluation, $artifact);
        
        return array_merge([
            'identification' => strtoupper($artifact->type),
            'origin_opinion' => 'Not requested',
            'test_method' => 'SOPOI',
            'report_number' => null,
        ], $extractedData);
    }

    /**
     * Extract actual evaluation data for certificate
     */
    private function extractEvaluationData($evaluation, $artifact)
    {
        $data = [];
        
        // استخراج البيانات الفعلية من التقييم
        if ($evaluation->weight) {
            $data['weight'] = floatval($evaluation->weight);
        } elseif ($artifact->weight) {
            $data['weight'] = floatval($artifact->weight);
        } else {
            $data['weight'] = 0.00;
        }
        
        if ($evaluation->colour) {
            $data['color'] = $evaluation->colour;
        } else {
            $data['color'] = 'Various';
        }
        
        if ($evaluation->shape_cut) {
            $data['shape_cut'] = $evaluation->shape_cut;
        } else {
            $data['shape_cut'] = 'Oval/Mix';
        }
        
        if ($evaluation->measurements) {
            $data['dimensions'] = $evaluation->measurements;
        } else {
            $data['dimensions'] = '0.00 x 0.00 x 0.00 mm';
        }
        
        if ($evaluation->transparency) {
            $data['transparency'] = $evaluation->transparency;
        } else {
            $data['transparency'] = 'Transparent';
        }
        
        if ($evaluation->phenomena) {
            $data['phenomena'] = $evaluation->phenomena;
        } else {
            $data['phenomena'] = '-';
        }
        
        if ($evaluation->species_group) {
            $data['species'] = $evaluation->species_group;
            $data['group'] = $evaluation->species_group;
        } else {
            $data['species'] = '-';
            $data['group'] = '-';
        }
        
        if ($evaluation->variety) {
            $data['variety'] = $evaluation->variety;
        } else {
            $data['variety'] = '-';
        }
        
        if ($evaluation->result) {
            $data['conclusion'] = $evaluation->result;
        } else {
            $data['conclusion'] = 'Pass';
        }
        
        if ($evaluation->comments) {
            $data['comments'] = $evaluation->comments;
        } else {
            $data['comments'] = 'Test evaluation for ' . $artifact->type;
        }
        
        return $data;
    }
}
