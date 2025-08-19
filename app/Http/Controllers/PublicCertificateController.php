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
            // For uploaded certificates, redirect to custom file route to avoid 403 errors
            $certificateFileUrl = url('/certificate-file/' . $certificate->uploaded_certificate_path);
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

    /**
     * Serve certificate file directly to avoid 403 errors
     */
    public function serveFile($filename)
    {
        try {
            $filePath = storage_path('app/public/' . $filename);
            
            // Log the request for debugging
            \Log::info('Certificate file access request', [
                'filename' => $filename,
                'full_path' => $filePath,
                'exists' => file_exists($filePath),
                'readable' => is_readable($filePath),
                'permissions' => file_exists($filePath) ? decoct(fileperms($filePath)) : 'N/A',
                'file_size' => file_exists($filePath) ? filesize($filePath) : 'N/A',
                'user_agent' => request()->header('User-Agent'),
                'ip' => request()->ip()
            ]);
            
            if (!file_exists($filePath)) {
                \Log::warning('Certificate file not found', ['path' => $filePath]);
                abort(404, 'Certificate file not found');
            }
            
            if (!is_readable($filePath)) {
                \Log::error('Certificate file not readable', ['path' => $filePath]);
                abort(403, 'Certificate file not accessible');
            }
            
            // For PDF files, serve inline
            if (pathinfo($filename, PATHINFO_EXTENSION) === 'pdf') {
                \Log::info('Serving PDF file successfully', ['path' => $filePath]);
                return response()->file($filePath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($filename) . '"',
                    'Cache-Control' => 'public, max-age=3600'
                ]);
            }
            
            // For other file types
            return response()->file($filePath);
            
        } catch (\Exception $e) {
            \Log::error('Error serving certificate file', [
                'filename' => $filename,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Error serving file: ' . $e->getMessage());
        }
    }

    /**
     * Show certificate search page
     */
    public function searchPage()
    {
        // إضافة CSRF token للصفحة
        return Inertia::render('Public/SearchCertificate', [
            'csrf_token' => csrf_token(),
        ]);
    }

    /**
     * Search for certificate by artifact code
     */
    public function searchCertificate(Request $request)
    {
        // Log that the function was called
        \Log::info('SearchCertificate function called', [
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url(),
            'user_agent' => $request->header('User-Agent'),
            'ip' => $request->ip()
        ]);

        $request->validate([
            'certificate_number' => 'required|string|max:100'
        ]);

        $artifactCode = $request->certificate_number; // We keep the field name for compatibility

        // Log the search request
        \Log::info('Certificate search request', [
            'artifact_code' => $artifactCode,
            'user_ip' => request()->ip()
        ]);

        // Search for artifact by code
        $artifact = Artifact::where('artifact_code', $artifactCode)
            ->where('status', 'certified')
            ->with(['client'])
            ->first();

        if (!$artifact) {
            \Log::warning('Artifact not found', ['artifact_code' => $artifactCode]);
            return back()->withErrors([
                'certificate_number' => 'Artifact not found or not yet certified.'
            ]);
        }

        \Log::info('Artifact found', [
            'artifact_id' => $artifact->id,
            'artifact_code' => $artifact->artifact_code,
            'status' => $artifact->status
        ]);

        // First, check if there's an uploaded certificate for this artifact
        $uploadedCertificate = Certificate::where('artifact_id', $artifact->id)
            ->where('status', 'uploaded')
            ->whereNotNull('uploaded_certificate_path')
            ->latest()
            ->first();

        \Log::info('Uploaded certificate search result', [
            'found' => $uploadedCertificate ? true : false,
            'status' => $uploadedCertificate ? $uploadedCertificate->status : 'N/A',
            'path' => $uploadedCertificate ? $uploadedCertificate->uploaded_certificate_path : 'N/A',
            'artifact_id' => $artifact->id
        ]);

        if ($uploadedCertificate) {
            // If uploaded certificate exists, return success with PDF URL
            $fileUrl = url('/certificate-file/' . $uploadedCertificate->uploaded_certificate_path);
            
            \Log::info('Certificate found, returning URL', [
                'path' => $uploadedCertificate->uploaded_certificate_path,
                'url' => $fileUrl
            ]);
            
            return Inertia::render('Public/SearchCertificate', [
                'success' => 'Certificate found! Click the button below to open it.',
                'open_pdf_url' => $fileUrl,
                'csrf_token' => csrf_token(),
            ]);
        }

        // If no uploaded certificate, get the latest generated certificate
        $certificate = Certificate::where('artifact_id', $artifact->id)
            ->with(['artifact.client', 'generatedBy'])
            ->latest()
            ->first();

        if (!$certificate) {
            \Log::warning('No certificate found for artifact', ['artifact_id' => $artifact->id]);
            return back()->withErrors([
                'certificate_number' => 'Certificate not found for this artifact.'
            ]);
        }

        // If no uploaded certificate, show info and redirect to generated certificate
        $certificateUrl = route('public.certificate.show', $certificate->qr_code_token ?? $certificate->id);
        
        \Log::info('Generated certificate found, returning URL', [
            'certificate_id' => $certificate->id,
            'url' => $certificateUrl
        ]);
        
        return Inertia::render('Public/SearchCertificate', [
            'info' => 'Generated certificate found. Click the button below to view it.',
            'open_pdf_url' => $certificateUrl,
            'csrf_token' => csrf_token(),
        ]);
    }
}
