<?php

namespace App\Livewire\Admin\Kyc;

use Livewire\Component;
use App\Models\KycSubmission;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $submissions = KycSubmission::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.kyc.index', [
            'submissions' => $submissions,
        ])->layout('components.layouts.admin');
    }
}
