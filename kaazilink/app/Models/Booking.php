<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'provider_id',
        'service_slug',
        'price',
        'status',
        'scheduled_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'scheduled_at' => 'datetime',
    ];

    /**
     * Relationship to Reschedule History
     */
    public function reschedules()
    {
        return $this->hasMany(BookingReschedule::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    // ==========================================
    // Phase 1 Sockets: Booking State Transitions
    // ==========================================

    public function accept()
    {
        // TODO: (Phase 2) Implement state transition logic, validations, and trigger Email Notifications / SMS
    }

    public function reject()
    {
        // TODO: (Phase 2) Implement state transition logic, notifications, and refund escrow if applicable
    }

    public function markInProgress()
    {
        // TODO: (Phase 2) Implement state transition logic when provider arrives/starts job
    }

    public function complete(?string $completedByRole = null)
    {
        $this->update(['status' => 'completed']);

        if (!$completedByRole) {
            $completedByRole = (auth()->check() && auth()->id() === $this->client_id) ? 'client' : 'provider';
        }

        if ($completedByRole === 'provider' && $this->client) {
            $this->client->notify(new \App\Notifications\BookingCompletedNotification($this, 'provider'));
        } elseif ($completedByRole === 'client' && $this->provider) {
            $this->provider->notify(new \App\Notifications\BookingCompletedNotification($this, 'client'));
        }
    }

    public function cancelByClient()
    {
        // TODO: (Phase 2) Implement logic for client cancellation, penalties, and M-PESA refund processing
    }

    public function cancelByProvider()
    {
        // TODO: (Phase 2) Implement logic for provider cancellation and full M-PESA refund to client
    }

    public function markNoShowClient()
    {
        // TODO: (Phase 2) Implement client no-show penalty deductions
    }

    public function markNoShowProvider()
    {
        // TODO: (Phase 2) Implement provider no-show penalties and client refunds
    }

    public function dispute()
    {
        // TODO: (Phase 2) Implement dispute logic, alerting admins and freezing payment dispatch
    }

    public function resolve()
    {
        // TODO: (Phase 2) Implement dispute resolution and final Daraja/M-PESA payout logic
    }

    public function refund()
    {
        // TODO: (Phase 2) Implement manual or automatic refund via payment gateway (Daraja STK/M-PESA)
    }

    public function reschedule($newScheduledAt, $requestedBy, $reason = null)
    {
        // TODO: (Phase 2) Implement reschedule logic, create BookingReschedule log entry, notify other party
    }
}
