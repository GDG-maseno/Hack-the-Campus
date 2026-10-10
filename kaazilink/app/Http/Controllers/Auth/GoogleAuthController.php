<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    protected function getRedirectUrl(): string
    {
        $configured = config('services.google.redirect');
        if (!empty($configured) && str_starts_with($configured, 'http')) {
            return $configured;
        }

        return url('/auth/google/callback');
    }

    public function redirect()
    {
        $redirectUrl = $this->getRedirectUrl();
        \Illuminate\Support\Facades\Log::info('Google Auth: Initiating redirect', [
            'configured_redirect' => config('services.google.redirect'),
            'resolved_redirectUrl' => $redirectUrl,
            'client_id_prefix' => substr(config('services.google.client_id') ?? '', 0, 15) . '...',
            'app_url' => config('app.url'),
        ]);

        return Socialite::driver('google')
            ->stateless()
            ->redirectUrl($redirectUrl)
            ->redirect();
    }

    public function callback()
    {
        $redirectUrl = $this->getRedirectUrl();
        \Illuminate\Support\Facades\Log::info('Google Auth: Handling callback', [
            'request_uri' => request()->fullUrl(),
            'has_code' => request()->has('code'),
            'has_error' => request()->has('error'),
            'error' => request()->get('error'),
            'resolved_redirectUrl' => $redirectUrl,
        ]);

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->redirectUrl($redirectUrl)
                ->user();

            \Illuminate\Support\Facades\Log::info('Google Auth: User retrieved successfully', [
                'email' => $googleUser->getEmail(),
                'id' => $googleUser->getId(),
            ]);

            // Find existing user by email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // If user exists, link Google ID if not already linked
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
                
                // If they hadn't verified their email yet (e.g. they registered via email previously but didn't click link), 
                // Google auth confirms email ownership.
                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }
            } else {
                // User does not exist, create new
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
                $user->markEmailAsVerified();
            }

            Auth::login($user, remember: true);
            request()->session()->regenerate();
            session()->save();

            \Illuminate\Support\Facades\Log::info('Google Auth: Successfully logged in user', [
                'user_id' => $user->id,
            ]);

            return redirect()->intended('/');
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Auth Failed', [
                'message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }
}
