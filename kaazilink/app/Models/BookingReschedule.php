<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingReschedule extends Model
{
    protected $fillable = [
        'booking_id',
        'original_scheduled_at',
        'new_scheduled_at',
        'requested_by',
        'reason',
    ];

    /**
     * Relationship to Booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
