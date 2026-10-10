<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'gateway',
        'amount',
        'phone',
        'status',
        'reference',
        'checkout_request_id',
        'transaction_id',
        'error_message',
        'raw_callback_payload',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw_callback_payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'timeout']);
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'initiated']);
    }
}
