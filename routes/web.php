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
    
    // Redirect /home to dashboard
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/reception', [ReceptionController::class, 'index'])->name('reception.index');
    Route::get('/reception/new', [ReceptionController::class, 'createClient'])->name('reception.new');
    Route::post('/reception/store', [ReceptionController::class, 'storeClient'])->name('reception.store');

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
