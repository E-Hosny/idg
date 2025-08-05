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
     * Display all certified artifacts
     */
    public function certified()
    {
        $artifacts = Artifact::with(['client', 'latestCertificate'])
            ->where('status', 'certified')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return Inertia::render('Dashboard/Certificates/Certified', [
            'artifacts' => $artifacts,
        ]);
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
