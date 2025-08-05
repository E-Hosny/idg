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
    
    // Certificate routes
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/artifact/{artifact}', [\App\Http\Controllers\CertificateController::class, 'index'])->name('artifact');
        Route::post('/generate/{artifact}', [\App\Http\Controllers\CertificateController::class, 'generate'])->name('generate');
        Route::get('/{certificate}', [\App\Http\Controllers\CertificateController::class, 'show'])->name('show');
        Route::post('/{certificate}/issue', [\App\Http\Controllers\CertificateController::class, 'issue'])->name('issue');
        Route::post('/{certificate}/regenerate-qr', [\App\Http\Controllers\CertificateController::class, 'regenerateQR'])->name('regenerate-qr');
        Route::get('/{certificate}/pdf', [\App\Http\Controllers\CertificateController::class, 'generatePDF'])->name('pdf');
        Route::get('/certified/list', [\App\Http\Controllers\CertificateController::class, 'certified'])->name('certified');
    });
    
    // Redirect /home to dashboard
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/reception', [ReceptionController::class, 'index'])->name('reception.index');
    Route::get('/reception/new', [ReceptionController::class, 'createClient'])->name('reception.new');
    Route::post('/reception/store', [ReceptionController::class, 'storeClient'])->name('reception.store');
    Route::get('/reception/client/{client}', [ReceptionController::class, 'showClient'])->name('reception.client.show');
    Route::get('/reception/client/{client}/artifact/new', [\App\Http\Controllers\ReceptionController::class, 'createArtifact'])->name('reception.artifact.create');
    Route::post('/reception/client/{client}/artifact/store', [\App\Http\Controllers\ReceptionController::class, 'storeArtifact'])->name('reception.artifact.store');
    Route::post('/reception/calculate-price', [\App\Http\Controllers\ReceptionController::class, 'calculatePrice'])->name('reception.calculate-price');
Route::get('/reception/test-pricing', [\App\Http\Controllers\ReceptionController::class, 'testPricing'])->name('reception.test-pricing');

    Route::get('/test-gate', function () {
        return [auth()->user()->role, \Gate::allows('isReceptionist')];
    });
});

// Language switcher
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// Public Certificate Routes (outside auth middleware)
Route::get('/certificate/{token}', [\App\Http\Controllers\PublicCertificateController::class, 'show'])->name('public.certificate.show');
Route::post('/certificate/verify', [\App\Http\Controllers\PublicCertificateController::class, 'verify'])->name('public.certificate.verify');

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
