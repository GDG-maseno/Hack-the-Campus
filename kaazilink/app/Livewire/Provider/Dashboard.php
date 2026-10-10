<?php

namespace App\Livewire\Provider;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ServiceArea;
use App\Models\ProviderService;
use App\Models\Booking;
use App\Data\ServiceDirectory;

class Dashboard extends Component
{
    public $activeTab = 'overview';

    // Services Management
    public $selectedServiceSlug = '';
    public $servicePrice = '';
    public $isCustomService = false;
    public $customServiceName = '';
    public $customServiceDescription = '';
    public $customServicePrice = '';
    public $editingServiceId = null;
    public $editPrice = '';

    // Areas Management
    public $selectedAreaId = '';

    // Bookings filter
    public $bookingStatusFilter = 'all';

    // Profile Management
    public $name = '';
    public $phone = '';
    public $id_number = '';
    public $address = '';
    public $years_of_experience = '';
    public $emergency_contact = '';
    public $bio = '';

    public function mount()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->isProvider()) {
            return redirect()->route('provider.onboarding');
        }

        $this->name = $user->name ?? '';
        $this->phone = $user->phone ?? '';
        $this->id_number = $user->id_number ?? '';
        $this->address = $user->address ?? '';
        $this->years_of_experience = $user->years_of_experience ?? '';
        $this->emergency_contact = $user->emergency_contact ?? '';
        $this->bio = $user->bio ?? '';
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    // --- Services Actions ---
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
            session()->flash('success', 'Custom service added successfully.');
            return;
        }

        $this->validate([
            'selectedServiceSlug' => 'required|string',
            'servicePrice' => 'required|numeric|min:100|max:1000000',
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
        session()->flash('success', 'Service added successfully.');
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
        session()->flash('success', 'Service price updated successfully.');
    }

    public function cancelEditPrice()
    {
        $this->editingServiceId = null;
        $this->editPrice = '';
    }

    public function removeService($serviceId)
    {
        Auth::user()->providerServices()->where('id', $serviceId)->delete();
        session()->flash('success', 'Service removed.');
    }

    // --- Area Actions ---
    public function addArea()
    {
        if (!$this->selectedAreaId) {
            return;
        }

        $areaId = $this->selectedAreaId;
        $user = Auth::user();
        if (!$user->serviceAreas()->where('service_area_id', $areaId)->exists()) {
            $user->serviceAreas()->attach($areaId);
        }

        $this->selectedAreaId = '';
        session()->flash('success', 'Coverage area added.');
    }

    public function removeArea($areaId)
    {
        Auth::user()->serviceAreas()->detach($areaId);
        session()->flash('success', 'Coverage area removed.');
    }

    // --- Booking Actions ---
    public function acceptBooking($bookingId)
    {
        $booking = Booking::where('provider_id', Auth::id())->findOrFail($bookingId);
        if ($booking->status === 'requested') {
            $booking->update(['status' => 'accepted']);
            session()->flash('success', 'Booking accepted.');
        }
    }

    public function completeBooking($bookingId)
    {
        $booking = Booking::where('provider_id', Auth::id())->findOrFail($bookingId);
        if (in_array($booking->status, ['requested', 'accepted'])) {
            $booking->complete();
            session()->flash('success', 'Booking marked as completed.');
        }
    }

    // --- Profile Actions ---
    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|min:2|max:255',
            'phone' => ['required', 'string', 'regex:/^(?:\+?254|0)[17]\d{8}$/'],
            'address' => 'required|string|min:4|max:255',
            'years_of_experience' => 'required|integer|min:0|max:50',
            'emergency_contact' => 'nullable|string|max:100',
            'bio' => 'required|string|min:20|max:1000',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'years_of_experience' => $this->years_of_experience,
            'emergency_contact' => $this->emergency_contact,
            'bio' => $this->bio,
        ]);

        session()->flash('success', 'Profile updated successfully.');
    }

    public function render()
    {
        $user = Auth::user();

        // Calculations & Metrics
        $totalBookings = Booking::where('provider_id', $user->id)->count();
        $completedBookings = Booking::where('provider_id', $user->id)->where('status', 'completed')->count();
        $pendingBookings = Booking::where('provider_id', $user->id)->where('status', 'requested')->count();
        $totalEarnings = Booking::where('provider_id', $user->id)
            ->where('status', 'completed')
            ->sum('price') ?: 0;

        $reviews = $user->reviewsAsProvider;
        $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 5.0;

        // Provider services
        $providerServices = $user->providerServices()->latest()->get();
        $standardSlugs = $providerServices->where('is_custom', false)->pluck('service_slug')->toArray();
        $allServices = collect(ServiceDirectory::getServices());
        $availableServices = $allServices->filter(function($service) use ($standardSlugs) {
            return !in_array($service['slug'], $standardSlugs);
        })->values();

        // Areas
        $providerAreas = $user->serviceAreas;
        $availableAreas = ServiceArea::whereNotIn('id', $providerAreas->pluck('id'))->get();

        // Bookings query
        $bookingsQuery = Booking::where('provider_id', $user->id)->with('client')->latest();
        if ($this->bookingStatusFilter !== 'all') {
            $bookingsQuery->where('status', $this->bookingStatusFilter);
        }
        $bookings = $bookingsQuery->get();

        return view('livewire.provider.dashboard', [
            'user' => $user,
            'totalBookings' => $totalBookings,
            'completedBookings' => $completedBookings,
            'pendingBookings' => $pendingBookings,
            'totalEarnings' => $totalEarnings,
            'avgRating' => $avgRating,
            'reviewsCount' => $reviews->count(),
            'providerServices' => $providerServices,
            'availableServices' => $availableServices,
            'providerAreas' => $providerAreas,
            'availableAreas' => $availableAreas,
            'bookings' => $bookings,
        ])->layout('components.layouts.app');
    }
}
