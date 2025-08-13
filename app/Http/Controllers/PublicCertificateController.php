<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Artifact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicCertificateController extends Controller
{
    /**
     * Display certificate by QR token
     */
    public function show($token)
    {
        $certificate = Certificate::where('qr_code_token', $token)
            ->with(['artifact.client', 'generatedBy'])
            ->first();

        if (!$certificate) {
            abort(404, 'Certificate not found.');
        }

        // Add status information for display
        $certificate->is_draft = $certificate->status === 'draft';

        return Inertia::render('Public/Certificate', [
            'certificate' => $certificate,
            'isPublic' => true,
        ]);
    }

    /**
     * Verify certificate authenticity
     */
    public function verify(Request $request)
    {
        $certificateNumber = $request->get('certificate_number');
        
        if (!$certificateNumber) {
            return response()->json([
                'valid' => false,
                'message' => 'Certificate number is required.'
            ]);
        }

        $certificate = Certificate::where('certificate_number', $certificateNumber)
            ->where('status', 'issued')
            ->with(['artifact.client'])
            ->first();

        if (!$certificate) {
            return response()->json([
                'valid' => false,
                'message' => 'Certificate not found or not yet issued.'
            ]);
        }

        return response()->json([
            'valid' => true,
            'certificate' => [
                'certificate_number' => $certificate->certificate_number,
                'identification' => $certificate->identification,
                'conclusion' => $certificate->conclusion,
                'issue_date' => $certificate->report_date->format('Y-m-d'),
                'client_name' => $certificate->artifact->client->full_name ?? 'N/A',
            ],
            'message' => 'Certificate is valid and authentic.'
        ]);
    }

    /**
     * Verify artifact by QR token and show certificate/evaluation
     */
    public function verifyArtifact($token)
    {
        $artifact = Artifact::where('qr_token', $token)
            ->with(['client', 'category'])
            ->first();

        if (!$artifact) {
            abort(404, 'Artifact not found or invalid QR code.');
        }

        // Check if artifact has uploaded certificate
        $certificate = Certificate::where('artifact_id', $artifact->id)
            ->where('status', 'uploaded')
            ->latest()
            ->first();

        if ($certificate && $certificate->uploaded_certificate_path) {
            // For uploaded certificates, redirect directly to the PDF file
            // This ensures QR codes embedded in printed certificates lead to the file
            $certificateFileUrl = asset('storage/' . $certificate->uploaded_certificate_path);
            return redirect($certificateFileUrl);
        }

        // If no uploaded certificate, check for evaluations
        $evaluation = null;
        $evaluationData = [];

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
            abort(404, 'No evaluation found for this artifact.');
        }

        // Show evaluation data as certificate format
        return Inertia::render('Public/ArtifactVerification', [
            'artifact' => $artifact,
            'evaluation' => $evaluation,
            'isPublic' => true,
        ]);
    }

    /**
     * Download uploaded certificate directly
     */
    public function downloadUploadedCertificate($token = null, Certificate $certificate = null)
    {
        // If token is provided, find certificate by artifact token
        if ($token) {
            $artifact = Artifact::where('qr_token', $token)->first();
            if (!$artifact) {
                abort(404, 'Artifact not found.');
            }
            
            $certificate = Certificate::where('artifact_id', $artifact->id)
                ->where('status', 'uploaded')
                ->latest()
                ->first();
        }

        if (!$certificate || !$certificate->uploaded_certificate_path) {
            abort(404, 'Certificate file not found.');
        }

        $filePath = storage_path('app/public/' . $certificate->uploaded_certificate_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Certificate file not found on disk.');
        }

        $artifact = $certificate->artifact;
        $fileName = 'certificate-' . $artifact->artifact_code . '.pdf';

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
