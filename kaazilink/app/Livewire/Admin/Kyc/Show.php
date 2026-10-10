<?php

namespace App\Livewire\Admin\Kyc;

use Livewire\Component;
use App\Models\KycSubmission;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    public KycSubmission $submission;
    public $rejectionReason = '';

    public function approve()
    {
        $this->submission->update(['status' => 'approved']);
        $this->submission->user->update(['kyc_status' => 'approved']);

        // Notify provider of approval
        $this->submission->user->notify(new \App\Notifications\KycApprovedNotification($this->submission));
        
        session()->flash('success', 'KYC approved successfully.');
        $this->redirect(route('admin.kyc.index'), navigate: true);
    }

    public function reject()
    {
        $this->validate([
            'rejectionReason' => 'required|min:10|max:500'
        ]);

        $this->submission->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
        ]);
        
        $this->submission->user->update(['kyc_status' => 'rejected']);

        // Notify provider of rejection with reason
        $this->submission->user->notify(new \App\Notifications\KycRejectedNotification($this->submission, $this->rejectionReason));
        
        session()->flash('success', 'KYC rejected.');
        $this->redirect(route('admin.kyc.index'), navigate: true);
    }

    public function getDocumentUrl($path)
    {
        if (!$path) return null;
        return route('kyc.document', ['path' => $path]);
    }

    public function render()
    {
        return view('livewire.admin.kyc.show')->layout('components.layouts.admin');
    }
}
