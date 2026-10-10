<?php

namespace App\Livewire\Provider;

use Livewire\Component;
use App\Models\ProviderService;
use App\Data\ServiceDirectory;
use Illuminate\Support\Facades\Auth;

class ProviderServicesManager extends Component
{
    public $selectedServiceSlug;

    public function addService()
    {
        if (!$this->selectedServiceSlug) {
            return;
        }

        $user = Auth::user();
        if (!$user->providerServices()->where('service_slug', $this->selectedServiceSlug)->exists()) {
            $user->providerServices()->create([
                'service_slug' => $this->selectedServiceSlug,
            ]);
        }
        
        $this->selectedServiceSlug = null;
    }

    public function removeService($slug)
    {
        Auth::user()->providerServices()->where('service_slug', $slug)->delete();
    }

    public function render()
    {
        $user = Auth::user();
        $providerServices = $user->providerServices->pluck('service_slug')->toArray();
        
        $allServices = collect(ServiceDirectory::getServices());
        
        // Exclude services they already have
        $availableServices = $allServices->filter(function($service) use ($providerServices) {
            return !in_array($service['slug'], $providerServices);
        });
        
        $options = [];
        foreach ($availableServices as $service) {
            $options[] = ['value' => $service['slug'], 'label' => $service['name']];
        }

        $myServices = $allServices->filter(function($service) use ($providerServices) {
            return in_array($service['slug'], $providerServices);
        });

        return view('livewire.provider.provider-services-manager', [
            'providerServices' => $myServices,
            'availableServicesOptions' => $options
        ])->layout('components.layouts.app');
    }
}
