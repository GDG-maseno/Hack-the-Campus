<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\VerifyEmail;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    
    // Google OAuth
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', VerifyEmail::class)->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/');
    })->middleware('signed')->name('verification.verify');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
    
    // Dedicated Provider Onboarding
    Route::get('/provider/onboarding', \App\Livewire\Provider\Onboarding::class)->name('provider.onboarding');

    // Become a Provider
    Route::get('/provider/become', function () {
        return redirect()->route('provider.onboarding');
    })->name('provider.become.get');

    Route::post('/provider/become', function () {
        $user = Auth::user();
        if ($user && $user->role === 'client') {
            $user->update(['role' => 'provider']);
        }
        return redirect()->route('provider.onboarding');
    })->name('provider.become');

    // Provider KYC Submit (Consolidated to unified 4-step onboarding flow)
    Route::get('/kyc/submit', function () {
        return redirect()->route('provider.onboarding');
    })->name('kyc.submit');
    
    // Protected Document Access
    Route::get('/kyc/document/{path}', [\App\Http\Controllers\KycDocumentController::class, 'show'])
        ->where('path', '.*')
        ->name('kyc.document');

    // Provider Dedicated Hub & Dashboard
    Route::get('/provider/dashboard', \App\Livewire\Provider\Dashboard::class)->name('provider.dashboard');

    // Provider Service Areas
    Route::get('/provider/service-areas', \App\Livewire\Provider\ServiceAreasManager::class)->name('provider.service-areas');
    Route::get('/provider/services', \App\Livewire\Provider\ProviderServicesManager::class)->name('provider.services');
    // Bookings Dashboard
    Route::get('/dashboard/bookings', \App\Livewire\Dashboard\Bookings::class)->name('dashboard.bookings');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/kyc', \App\Livewire\Admin\Kyc\Index::class)->name('kyc.index');
    Route::get('/kyc/{submission}', \App\Livewire\Admin\Kyc\Show::class)->name('kyc.show');
    Route::get('/settings', \App\Livewire\Admin\Settings\Index::class)->name('settings.index');
    Route::get('/metrics', \App\Livewire\Admin\Metrics\Index::class)->name('metrics.index');
});

// Phase 1 "Sockets": Foundational Livewire Component Routes
// These currently use the stubbed components in App\Livewire

Route::get('/', \App\Livewire\Pages\Home::class)->name('home');
Route::get('/how-it-works', \App\Livewire\Pages\HowItWorks::class)->name('how-it-works');

Route::get('/services', \App\Livewire\ServiceCategory\Browse::class)->name('services.browse');
Route::get('/services/{slug}', \App\Livewire\ServiceCategory\Show::class)->name('services.show');

Route::get('/provider/{slug}', \App\Livewire\Provider\Profile::class)->name('provider.profile');

// Legal & Trust Routes
Route::get('/terms', \App\Livewire\Pages\Terms::class)->name('terms');
Route::get('/privacy', \App\Livewire\Pages\Privacy::class)->name('privacy');

// External payment webhook callback route (matches /payments/callback/payhero in addition to /api/payments/callback/payhero)
Route::post('/payments/callback/payhero', \App\Http\Controllers\Payments\PayHeroCallbackController::class)->name('payments.callback.payhero.web');

// Lightweight Keep-Alive / Ping Endpoint (prevents Render free tier spin-down)
Route::get('/ping', function () {
    return response('pong', 200)
        ->header('Content-Type', 'text/plain')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
})->name('ping');
