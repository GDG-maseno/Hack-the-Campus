<?php

namespace App\Livewire\Review;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class CreateModal extends Component
{
    public $bookingId;
    public $isOpen = false;
    
    public $rating = 5;
    public $comment = '';

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ];

    #[On('openReviewModal')]
    public function openModal($bookingId)
    {
        $this->bookingId = $bookingId;
        $this->reset(['rating', 'comment']);
        $this->dispatch('wirekit-modal-show', name: 'review-create-modal');
    }

    public function submit()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate();

        $booking = Booking::findOrFail($this->bookingId);
        
        // Ensure user is the client
        if ($booking->client_id !== Auth::id()) {
            abort(403);
        }
        
        // Ensure booking is completed
        if ($booking->status !== 'completed') {
            session()->flash('error', 'You can only review completed bookings.');
            return;
        }
        
        // Ensure not already reviewed
        if ($booking->review()->exists()) {
            session()->flash('error', 'You have already reviewed this booking.');
            return;
        }

        $review = Review::create([
            'booking_id' => $booking->id,
            'client_id' => $booking->client_id,
            'provider_id' => $booking->provider_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        // Notify provider of new review
        if ($booking->provider) {
            $booking->provider->notify(new \App\Notifications\NewReviewNotification($review));
        }

        $this->dispatch('wirekit-modal-close', name: 'review-create-modal');
        session()->flash('success', 'Review submitted successfully!');
        $this->dispatch('reviewCreated');
    }

    public function render()
    {
        $booking = $this->bookingId ? Booking::with('provider')->find($this->bookingId) : null;

        return view('livewire.review.create-modal', [
            'booking' => $booking
        ]);
    }
}
