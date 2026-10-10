<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Terms extends Component
{
    public function render()
    {
        return view('pages.terms')->layout('components.layouts.app', ['title' => 'Terms of Service — Kaazilink']);
    }
}