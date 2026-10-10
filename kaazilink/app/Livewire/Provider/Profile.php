<?php

namespace App\Livewire\Provider;

use Livewire\Component;

class Profile extends Component
{
    public $slug;

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    public function bookNow($providerId)
    {
        $this->dispatch('openBookingModal', providerId: $providerId);
    }

    public function leaveReview($bookingId)
    {
        $this->dispatch('openReviewModal', bookingId: $bookingId);
    }

    public function render()
    {
        // Enforce gating: only approved providers can be viewed
        $provider = \App\Models\User::approvedProvider()->with(['serviceAreas', 'providerServices', 'reviewsAsProvider.client'])->findOrFail($this->slug);
        
        $providerServices = $provider->providerServices;

        $reviews = $provider->reviewsAsProvider()->latest()->get();
        $averageRating = $reviews->avg('rating') ?: 0;

        $canReview = false;
        $bookingIdToReview = null;

        if (\Illuminate\Support\Facades\Auth::check()) {
            $userId = \Illuminate\Support\Facades\Auth::id();
            
            // Find a completed booking for this user with this provider that hasn't been reviewed yet
            $unreviewedBooking = \App\Models\Booking::where('client_id', $userId)
                ->where('provider_id', $provider->id)
                ->where('status', 'completed')
                ->whereDoesntHave('review')
                ->first();

            if ($unreviewedBooking) {
                $canReview = true;
                $bookingIdToReview = $unreviewedBooking->id;
            }
        }

        $mapMarkers = $provider->serviceAreas->map(function($area) use ($provider) {
            return [
                'id' => $provider->id . '-' . $area->id,
                'lat' => $area->latitude,
                'lng' => $area->longitude,
                'label' => $provider->name,
                'body' => $area->name,
            ];
        })->toArray();

        return view('livewire.provider.profile', [
            'provider' => $provider,
            'mapMarkers' => $mapMarkers,
            'services' => $providerServices,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'canReview' => $canReview,
            'bookingIdToReview' => $bookingIdToReview,
        ])->layout('components.layouts.app');
    }
}
