<?php

namespace App\Livewire\Provider;

use Livewire\Component;
use App\Models\ServiceArea;
use Illuminate\Support\Facades\Auth;

class ServiceAreasManager extends Component
{
    public $selectedAreaId;

    public function addArea()
    {
        if (!$this->selectedAreaId) {
            return;
        }

        $areaId = $this->selectedAreaId;
        if (!is_numeric($areaId)) {
            $area = ServiceArea::where('name', $areaId)->first();
            if ($area) {
                $areaId = $area->id;
            } else {
                return;
            }
        }

        $user = Auth::user();
        if (!$user->serviceAreas()->where('service_area_id', $areaId)->exists()) {
            $user->serviceAreas()->attach($areaId);
        }
        
        $this->selectedAreaId = null;
    }

    public function removeArea($areaId)
    {
        Auth::user()->serviceAreas()->detach($areaId);
    }

    public function render()
    {
        $user = Auth::user();
        $providerAreas = $user->serviceAreas;
        
        // Exclude areas they already have
        $availableAreas = ServiceArea::whereNotIn('id', $providerAreas->pluck('id'))->get();
        
        $options = [];
        foreach ($availableAreas as $area) {
            $options[] = ['value' => $area->id, 'label' => $area->name];
        }

        return view('livewire.provider.service-areas-manager', [
            'providerAreas' => $providerAreas,
            'availableAreasOptions' => $options
        ])->layout('components.layouts.app');
    }
}
