<?php

namespace App\Livewire\Provider;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ServiceArea;
use App\Models\ProviderService;
use App\Data\ServiceDirectory;

class Onboarding extends Component
{
    use WithFileUploads;

    public $currentStep = 1;

    // Step 1: Personal Details
    public $name = '';
    public $phone = '';
    public $id_number = '';
    public $date_of_birth = '';
    public $address = '';
    public $years_of_experience = '';
    public $emergency_contact = '';
    public $bio = '';

    // Step 2: Services & Pricing
    public $selectedServiceSlug = '';
    public $servicePrice = '';
    public $isCustomService = false;
    public $customServiceName = '';
    public $customServiceDescription = '';
    public $customServicePrice = '';

    public $editingServiceId = null;
    public $editPrice = '';

    // Step 3: Service Areas
    public $selectedAreaId = '';

    // Step 4: KYC Documents
    public $idFront;
    public $idBack;
    public $kraPin;

    public function mount()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Prepopulate personal details
        $this->name = $user->name ?? '';
        $this->phone = $user->phone ?? '';
        $this->id_number = $user->id_number ?? '';
        $this->date_of_birth = $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '';
        $this->address = $user->address ?? '';
        $this->years_of_experience = $user->years_of_experience ?? '';
        $this->emergency_contact = $user->emergency_contact ?? '';
        $this->bio = $user->bio ?? '';

        // If client, mark role as provider so they can configure their profile.
        // Never overwrite the 'admin' role.
        if ($user->role === 'client') {
            $user->update(['role' => 'provider']);
        }

        // Resume at appropriate step
        if ($user->kyc_status === 'pending' || $user->kyc_status === 'approved') {
            $this->currentStep = 4;
        } elseif ($user->serviceAreas()->exists()) {
            $this->currentStep = 4;
        } elseif ($user->providerServices()->exists()) {
            $this->currentStep = 3;
        } elseif (!empty($user->id_number) && !empty($user->phone)) {
            $this->currentStep = 2;
        } else {
            $this->currentStep = 1;
        }
    }

    public function setStep($step)
    {
        $this->currentStep = max(1, min(4, $step));
    }

    public function savePersonalDetails()
    {
        $this->validate([
            'name' => 'required|string|min:2|max:255',
            'phone' => ['required', 'string', 'regex:/^(?:\+?254|0)[17]\d{8}$/'],
            'id_number' => 'required|string|min:6|max:10|regex:/^\d+$/',
            'date_of_birth' => 'required|date|before:-18 years',
            'address' => 'required|string|min:4|max:255',
            'years_of_experience' => 'required|integer|min:0|max:50',
            'emergency_contact' => 'nullable|string|max:100',
            'bio' => 'required|string|min:20|max:1000',
        ], [
            'phone.regex' => 'Please enter a valid Kenyan phone number (e.g. 0712345678 or +254712345678).',
            'id_number.regex' => 'National ID must contain 6 to 10 digits.',
            'date_of_birth.before' => 'You must be at least 18 years old to register as a service provider.',
            'bio.min' => 'Please provide a bio of at least 20 characters describing your skills.',
        ]);

        $user = Auth::user();
        $user->update([
            'name'                => $this->name,
            'phone'               => $this->phone,
            'id_number'           => $this->id_number,
            'date_of_birth'       => $this->date_of_birth,
            'address'             => $this->address,
            'years_of_experience' => $this->years_of_experience,
            'emergency_contact'   => $this->emergency_contact,
            'bio'                 => $this->bio,
            // Only promote to provider — never demote or overwrite the admin role.
            'role'                => $user->isAdmin() ? 'admin' : 'provider',
        ]);

        session()->flash('personal_success', 'Personal details saved successfully.');
        $this->currentStep = 2;
    }

    public function addCustomService()
    {
        $this->isCustomService = true;
        $this->addService();
    }

    public function addService()
    {
        if ($this->isCustomService) {
            $this->validate([
                'customServiceName' => 'required|string|min:3|max:100',
                'customServiceDescription' => 'nullable|string|max:500',
                'customServicePrice' => 'required|numeric|min:100|max:1000000',
            ]);

            $slug = 'custom-' . Str::slug($this->customServiceName) . '-' . substr(uniqid(), -4);
            $user = Auth::user();

            $user->providerServices()->create([
                'service_slug' => $slug,
                'custom_name' => $this->customServiceName,
                'custom_description' => $this->customServiceDescription ?: "Custom {$this->customServiceName} service provided by {$user->name}.",
                'price' => $this->customServicePrice,
                'is_custom' => true,
            ]);

            $this->reset(['customServiceName', 'customServiceDescription', 'customServicePrice', 'isCustomService']);
            session()->flash('service_success', 'Custom service added successfully.');
            return;
        }

        $this->validate([
            'selectedServiceSlug' => 'required|string',
            'servicePrice' => 'required|numeric|min:100|max:1000000',
        ], [
            'selectedServiceSlug.required' => 'Please select a service from the list.',
            'servicePrice.required' => 'Please enter your price for this service.',
            'servicePrice.numeric' => 'Price must be a valid number in KES.',
        ]);

        $user = Auth::user();
        if (!$user->providerServices()->where('service_slug', $this->selectedServiceSlug)->exists()) {
            $user->providerServices()->create([
                'service_slug' => $this->selectedServiceSlug,
                'price' => $this->servicePrice,
                'is_custom' => false,
            ]);
        }

        $this->selectedServiceSlug = '';
        $this->servicePrice = '';
        session()->flash('service_success', 'Service added with your custom price.');
    }

    public function startEditPrice($serviceId, $currentPrice)
    {
        $this->editingServiceId = $serviceId;
        $this->editPrice = $currentPrice;
    }

    public function saveEditPrice($serviceId)
    {
        $this->validate([
            'editPrice' => 'required|numeric|min:100|max:1000000',
        ]);

        $ps = Auth::user()->providerServices()->findOrFail($serviceId);
        $ps->update(['price' => $this->editPrice]);

        $this->editingServiceId = null;
        $this->editPrice = '';
        session()->flash('service_success', 'Service price updated successfully.');
    }

    public function cancelEditPrice()
    {
        $this->editingServiceId = null;
        $this->editPrice = '';
    }

    public function removeService($serviceId)
    {
        Auth::user()->providerServices()->where('id', $serviceId)->delete();
        session()->flash('service_success', 'Service removed.');
    }

    public function continueToAreas()
    {
        $user = Auth::user();
        if ($user->providerServices()->count() === 0) {
            $this->addError('services_required', 'Please add at least one service before proceeding.');
            return;
        }

        $this->currentStep = 3;
    }

    public function addArea()
    {
        if (!$this->selectedAreaId) {
            return;
        }

        $areaId = $this->selectedAreaId;
        if (!is_numeric($areaId)) {
            $area = ServiceArea::where('name', $areaId)
                ->orWhereRaw('LOWER(name) = ?', [strtolower(trim($areaId))])
                ->first();
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

        $this->selectedAreaId = '';
        session()->flash('area_success', 'Service area added successfully.');
    }

    public function removeArea($areaId)
    {
        Auth::user()->serviceAreas()->detach($areaId);
    }

    public function continueToKyc()
    {
        $user = Auth::user();
        if ($user->serviceAreas()->count() === 0) {
            $this->addError('areas_required', 'Please add at least one service area before proceeding.');
            return;
        }

        $this->currentStep = 4;
    }

    public function submitKyc()
    {
        $this->validate([
            'idFront' => 'required|image|max:5120', // 5MB Max
            'idBack' => 'required|image|max:5120',
            'kraPin' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'idFront.required' => 'National ID (Front) photo is required.',
            'idFront.image' => 'The file must be a valid JPG or PNG image.',
            'idFront.max' => 'The file size must not exceed 5MB.',
            'idBack.required' => 'National ID (Back) photo is required.',
            'idBack.image' => 'The file must be a valid JPG or PNG image.',
            'idBack.max' => 'The file size must not exceed 5MB.',
            'kraPin.required' => 'KRA PIN Certificate is required.',
            'kraPin.mimes' => 'KRA PIN must be a PDF, JPG, or PNG document.',
            'kraPin.max' => 'The file size must not exceed 5MB.',
        ]);

        $user = Auth::user();

        // Store in the 'kyc' disk
        $idFrontPath = $this->idFront->store($user->id, 'kyc');
        $idBackPath = $this->idBack->store($user->id, 'kyc');
        $kraPinPath = $this->kraPin->store($user->id, 'kyc');

        $submission = $user->kycSubmissions()->create([
            'id_front_path' => $idFrontPath,
            'id_back_path' => $idBackPath,
            'kra_pin_path' => $kraPinPath,
            'status' => 'pending',
        ]);

        // Never overwrite the 'admin' role when marking KYC as pending.
        $user->update([
            'kyc_status' => 'pending',
            'role'       => $user->isAdmin() ? 'admin' : 'provider',
        ]);

        // Notify admins of pending KYC submission
        $admins = \App\Models\User::where('role', 'admin')->get();
        if ($admins->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\KycPendingReviewNotification($submission));
        } else {
            \Illuminate\Support\Facades\Notification::route('mail', config('mail.from.address'))
                ->notify(new \App\Notifications\KycPendingReviewNotification($submission));
        }

        session()->flash('kyc_success', 'KYC documents submitted successfully! Our team will review your application shortly.');
    }

    public function render()
    {
        $user = Auth::user();
        $providerServices = $user->providerServices()->get();
        $standardSlugs = $providerServices->where('is_custom', false)->pluck('service_slug')->toArray();
        $allServices = collect(ServiceDirectory::getServices());

        $availableServices = $allServices->filter(function($service) use ($standardSlugs) {
            return !in_array($service['slug'], $standardSlugs);
        })->values();

        $providerAreas = $user->serviceAreas()->get();
        $availableAreas = ServiceArea::whereNotIn('id', $providerAreas->pluck('id'))->get();

        return view('livewire.provider.onboarding', [
            'user' => $user,
            'providerServices' => $providerServices,
            'availableServices' => $availableServices,
            'providerAreas' => $providerAreas,
            'availableAreas' => $availableAreas,
        ])->layout('components.layouts.app');
    }
}
