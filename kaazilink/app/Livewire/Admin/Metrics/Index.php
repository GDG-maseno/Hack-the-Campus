<?php

namespace App\Livewire\Admin\Metrics;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.admin.metrics.index')->layout('components.layouts.admin');
    }
}
