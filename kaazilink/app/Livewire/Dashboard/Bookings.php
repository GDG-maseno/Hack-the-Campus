<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class Bookings extends Component
{
    public function markCompleted($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        $user = Auth::user();
        
        // Authorization: only the client or provider can mark complete
        if ($booking->client_id !== $user->id && $booking->provider_id !== $user->id) {
            abort(403);
        }
        
        if (in_array($booking->status, ['requested', 'accepted'])) {
            $booking->complete();
            session()->flash('success', 'Booking marked as completed.');
        }
    }

    public function leaveReview($bookingId)
    {
        $this->dispatch('openReviewModal', bookingId: $bookingId);
    }

    public function render()
    {
        $user = Auth::user();
        
        $bookings = Booking::where('client_id', $user->id)
            ->orWhere('provider_id', $user->id)
            ->with(['client', 'provider'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.dashboard.bookings', [
            'bookings' => $bookings
        ])->layout('components.layouts.app');
    }
}
