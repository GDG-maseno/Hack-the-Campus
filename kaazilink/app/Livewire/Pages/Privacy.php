<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Privacy extends Component
{
    public function render()
    {
        return view('pages.privacy')->layout('components.layouts.app', ['title' => 'Privacy Policy — Kaazilink']);
    }
}