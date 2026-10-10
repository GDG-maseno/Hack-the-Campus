<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\SiteSetting;

class Index extends Component
{
    public $commission_percentage = 10;
    public $support_email = 'hello@example.com';
    public $featured_categories = [];

    public function mount()
    {
        $this->commission_percentage = SiteSetting::where('key', 'commission_percentage')->value('value') ?? 10;
        $this->support_email = SiteSetting::where('key', 'support_email')->value('value') ?? 'hello@example.com';
        $this->featured_categories = SiteSetting::where('key', 'featured_categories')->value('value') ?? [];
    }

    public function save()
    {
        $this->validate([
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'support_email' => 'required|email',
            'featured_categories' => 'array',
        ]);

        SiteSetting::updateOrCreate(['key' => 'commission_percentage'], ['value' => $this->commission_percentage]);
        SiteSetting::updateOrCreate(['key' => 'support_email'], ['value' => $this->support_email]);
        SiteSetting::updateOrCreate(['key' => 'featured_categories'], ['value' => $this->featured_categories]);

        session()->flash('success', 'Site settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.index')->layout('components.layouts.admin');
    }
}
