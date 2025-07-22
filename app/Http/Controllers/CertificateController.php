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
            $evaluation = $artifact->evaluations()
                ->where('is_final', true)
                ->latest()
                ->first();
                
            if ($evaluation) {
                $evaluationData = $this->mapGeneralEvaluationData($evaluation, $artifact);
            }
        }

        if (!$evaluation) {
            return back()->withErrors(['error' => 'No completed evaluation found for this artifact.']);
        }

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
            'weight' => $evaluation->weight,
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
        return [
            'identification' => strtoupper($artifact->type),
            'shape_cut' => 'Oval/Mix',
            'weight' => $artifact->weight,
            'color' => 'Various',
            'dimensions' => null,
            'transparency' => 'Transparent',
            'phenomena' => null,
            'species' => '-',
            'variety' => '-',
            'group' => '-',
            'origin_opinion' => 'Not requested',
            'conclusion' => $evaluation->result ?? 'Natural',
            'comments' => $evaluation->comments,
            'test_method' => 'SOPOI',
            'report_number' => null,
        ];
    }
}
