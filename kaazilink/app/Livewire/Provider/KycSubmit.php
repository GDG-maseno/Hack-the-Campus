<?php

namespace App\Livewire\Provider;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class KycSubmit extends Component
{
    use WithFileUploads;

    public $idFront;
    public $idBack;
    public $kraPin;

    public function mount()
    {
        $user = Auth::user();
        if ($user->kyc_status === 'pending') {
            session()->flash('info', 'Your KYC submission is currently under review.');
            $this->redirect(route('home'), navigate: true);
        } elseif ($user->kyc_status === 'approved') {
            session()->flash('success', 'Your KYC has already been approved.');
            $this->redirect(route('home'), navigate: true);
        }
    }

    public function submit()
    {
        $this->validate([
            'idFront' => 'required|image|max:5120', // 5MB Max
            'idBack' => 'required|image|max:5120',
            'kraPin' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $user = Auth::user();

        // Store in the 'kyc' local private disk
        $idFrontPath = $this->idFront->store($user->id, 'kyc');
        $idBackPath = $this->idBack->store($user->id, 'kyc');
        $kraPinPath = $this->kraPin->store($user->id, 'kyc');

        $user->kycSubmissions()->create([
            'id_front_path' => $idFrontPath,
            'id_back_path' => $idBackPath,
            'kra_pin_path' => $kraPinPath,
            'status' => 'pending',
        ]);

        $user->update(['kyc_status' => 'pending', 'role' => 'provider']);

        session()->flash('success', 'KYC documents submitted successfully. We will review them shortly.');
        return $this->redirect(route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.provider.kyc-submit')->layout('components.layouts.app');
    }
}
