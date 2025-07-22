<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
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
}
