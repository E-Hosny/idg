<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReceptionController;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Test endpoint for debugging (public)
Route::get('/test-evaluation', [\App\Http\Controllers\TestController::class, 'testEvaluation'])->name('test.evaluation');

// Authentication routes (manual routes instead of Auth::routes())
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/artifacts', [DashboardController::class, 'artifacts'])->name('dashboard.artifacts');
    Route::get('/dashboard/evaluations', [DashboardController::class, 'evaluations'])->name('dashboard.evaluations');
    Route::get('/dashboard/categories', [DashboardController::class, 'categories'])->name('dashboard.categories');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('/dashboard/artifacts/{artifact}/evaluate', [\App\Http\Controllers\DashboardController::class, 'evaluate'])->name('dashboard.artifacts.evaluate');
    Route::post('/dashboard/artifacts/{artifact}/evaluate', [\App\Http\Controllers\DashboardController::class, 'storeEvaluation'])->name('dashboard.artifacts.evaluate.store');
    Route::get('/dashboard/artifacts/{artifact}/evaluation', [\App\Http\Controllers\DashboardController::class, 'showEvaluation'])->name('dashboard.artifacts.evaluation.show');
    
    Route::get('/dashboard/evaluated-artifacts', [\App\Http\Controllers\DashboardController::class, 'evaluatedArtifacts'])->name('dashboard.evaluated-artifacts');
    
    // Reception routes
    Route::get('/reception', [ReceptionController::class, 'index'])->name('reception.index');
    Route::get('/reception/new-client', [ReceptionController::class, 'createClient'])->name('reception.new-client');
    Route::post('/reception/store-client', [ReceptionController::class, 'storeClient'])->name('reception.store-client');
    Route::get('/reception/clients/{client}', [ReceptionController::class, 'showClient'])->name('reception.show-client');
    Route::get('/reception/clients/{client}/add-artifact', [ReceptionController::class, 'createArtifact'])->name('reception.artifact.create');
    Route::post('/reception/clients/{client}/store-artifact', [ReceptionController::class, 'storeArtifact'])->name('reception.artifact.store');
    Route::post('/reception/calculate-price', [ReceptionController::class, 'calculatePrice'])->name('reception.calculate-price');
    Route::get('/reception/test-pricing', [ReceptionController::class, 'testPricing'])->name('reception.test-pricing');
    
    // Certificate routes
    Route::get('/certificates/{certificate}', [\App\Http\Controllers\CertificateController::class, 'show'])->name('certificates.show');
    Route::post('/certificates/{artifact}/generate', [\App\Http\Controllers\CertificateController::class, 'generate'])->name('certificates.generate');
    Route::get('/certificates/certified/list', [\App\Http\Controllers\CertificateController::class, 'certified'])->name('certificates.certified.list');
    
    // New QR and Certificate Upload routes
    Route::get('/artifacts/{artifact}/download-qr', [\App\Http\Controllers\CertificateController::class, 'downloadQR'])->name('artifacts.download-qr');
    Route::post('/artifacts/{artifact}/upload-certificate', [\App\Http\Controllers\CertificateController::class, 'uploadCertificate'])->name('artifacts.upload-certificate');
    
    // Public certificate route (no auth required)
    Route::get('/public/certificate/{certificate}', [\App\Http\Controllers\PublicCertificateController::class, 'show'])->name('public.certificate.show');
});

// Public certificate route (no auth required)
Route::get('/public/certificate/{certificate}', [\App\Http\Controllers\PublicCertificateController::class, 'show'])->name('public.certificate.show');

// Public artifact verification route
Route::get('/verify-artifact/{token}', [\App\Http\Controllers\PublicCertificateController::class, 'verifyArtifact'])->name('public.verify-artifact');

// Direct certificate download route
Route::get('/download-certificate/{token}', [\App\Http\Controllers\PublicCertificateController::class, 'downloadUploadedCertificate'])->name('public.download-certificate');

// Custom file access route to avoid 403 errors
Route::get('/files/{filename}', function($filename) {
    $path = storage_path('app/public/' . $filename);
    
    // Log the request for debugging
    \Log::info('File access request', [
        'filename' => $filename,
        'full_path' => $path,
        'exists' => file_exists($path),
        'readable' => is_readable($path),
        'permissions' => decoct(fileperms($path))
    ]);
    
    if (file_exists($path) && is_readable($path)) {
        // Set proper headers for PDF files
        if (pathinfo($filename, PATHINFO_EXTENSION) === 'pdf') {
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($filename) . '"'
            ]);
        }
        return response()->file($path);
    }
    
    abort(404, 'File not found or not accessible');
})->name('files.show');

// Alternative file access route using controller method
Route::get('/certificate-file/{filename}', [\App\Http\Controllers\PublicCertificateController::class, 'serveFile'])->where('filename', '.*')->name('certificate.file');

// Quick test route for PDF files (for debugging)
Route::get('/test-pdf-file/{filename}', function($filename) {
    $path = storage_path('app/public/' . $filename);
    
    if (!file_exists($path)) {
        return response()->json([
            'error' => 'File not found',
            'path' => $path,
            'exists' => false
        ], 404);
    }
    
    if (!is_readable($path)) {
        return response()->json([
            'error' => 'File not readable',
            'path' => $path,
            'permissions' => decoct(fileperms($path))
        ], 403);
    }
    
    // Return file info for testing
    return response()->json([
        'success' => true,
        'filename' => $filename,
        'path' => $path,
        'exists' => true,
        'readable' => true,
        'size' => filesize($path),
        'permissions' => decoct(fileperms($path)),
        'url' => url('/certificate-file/' . $filename)
    ]);
})->name('test.pdf.file');

// Language switcher
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// Temporary test route for PDF (remove in production)
Route::get('/test-pdf/{id}', function($id) {
    $certificate = \App\Models\Certificate::find($id);
    if (!$certificate) {
        abort(404);
    }
    
    $data = [
        'certificate' => $certificate->load(['artifact.client', 'generatedBy']),
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('minimal-test', $data)
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
    
    // Test the improved PDF template
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.pdf', $data)
        ->setPaper('a4', 'portrait');
    
    return $pdf->download("certificate-{$certificate->certificate_number}.pdf");
});
