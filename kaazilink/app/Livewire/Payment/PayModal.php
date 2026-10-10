<?php

namespace App\Livewire\Payment;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentManager;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class PayModal extends Component
{
    public ?int $bookingId = null;
    public ?Booking $booking = null;
    public string $phone = '';
    public ?Payment $currentPayment = null;
    public ?string $errorMessage = null;
    public bool $isInitiated = false;

    // Internal: gateway is resolved internally — never surfaced to the user
    protected string $resolvedGateway = '';

    #[On('openPaymentModal')]
    public function openModal(int $bookingId, PaymentManager $paymentManager)
    {
        $this->bookingId = $bookingId;
        $this->booking = Booking::with(['client', 'provider', 'payments'])->findOrFail($bookingId);

        // Security: only the booking's client can initiate payment
        if ($this->booking->client_id !== Auth::id()) {
            abort(403, 'Unauthorized to pay for this booking.');
        }

        // Resolve gateway internally — not exposed to the UI
        $this->resolvedGateway = $paymentManager->resolveGatewayForBooking($this->booking);

        // Pre-populate phone from auth user
        $this->phone = Auth::user()->phone ?? '';
        $this->errorMessage = null;
        $this->isInitiated = false;
        $this->currentPayment = $this->booking->payments()->latest()->first();

        $this->dispatch('wirekit-modal-show', name: 'payment-modal');
    }

    public function initiatePayment(PaymentManager $paymentManager)
    {
        $this->validate([
            'phone' => ['required', 'string', 'regex:/^(?:\+?254|0)[17]\d{8}$/'],
        ], [
            'phone.regex' => 'Please enter a valid Kenyan Safaricom phone number (e.g. 0712345678 or 254712345678).',
        ]);

        if (!$this->booking || empty($this->booking->price) || $this->booking->price <= 0) {
            $this->errorMessage = 'This booking does not have a valid payable amount.';
            return;
        }

        try {
            $this->currentPayment = $paymentManager->initiateBookingPayment($this->booking, $this->phone);

            if ($this->currentPayment->status === 'initiated') {
                $this->isInitiated = true;
                $this->errorMessage = null;
            } else {
                $this->errorMessage = $this->currentPayment->error_message ?: 'Failed to initiate M-PESA payment. Please try again.';
            }
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    /**
     * Poll the database for the current payment's status.
     * Called by Livewire wire:poll every 4 seconds when $isInitiated is true.
     */
    public function checkPaymentStatus()
    {
        if (!$this->currentPayment) {
            return;
        }

        // Re-fetch fresh state from the database
        $fresh = Payment::find($this->currentPayment->id);
        if (!$fresh) {
            return;
        }

        $this->currentPayment = $fresh;

        // If the payment has moved out of 'initiated', stop polling
        if (!in_array($fresh->status, ['pending', 'initiated'])) {
            $this->isInitiated = false;
        }
    }

    /**
     * Reset state so the user can retry after a failure or cancellation.
     */
    public function resetPayment()
    {
        $this->isInitiated = false;
        $this->errorMessage = null;
        $this->currentPayment = $this->booking?->payments()->latest()->first();
    }

    public function render()
    {
        return view('livewire.payment.pay-modal');
    }
}
