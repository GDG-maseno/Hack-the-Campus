<?php

namespace App\Livewire\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Livewire\Component;

class VerifyEmail extends Component
{
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/');
        }

        $request->user()->sendEmailVerificationNotification();

        session()->flash('message', 'Verification link sent!');
    }

    public function render()
    {
        return view('livewire.auth.verify-email')->layout('components.layouts.app');
    }
}
