<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 
    'email', 
    'password', 
    'google_id', 
    'avatar', 
    'role', 
    'kyc_status',
    'phone',
    'bio',
    'id_number',
    'date_of_birth',
    'address',
    'years_of_experience',
    'emergency_contact',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'years_of_experience' => 'integer',
        ];
    }

    public function kycSubmissions()
    {
        return $this->hasMany(KycSubmission::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function scopeApprovedProvider($query)
    {
        return $query->where('role', 'provider')
                     ->where('kyc_status', 'approved');
    }

    public function serviceAreas()
    {
        return $this->belongsToMany(ServiceArea::class);
    }

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class);
    }

    public function bookingsAsClient()
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function bookingsAsProvider()
    {
        return $this->hasMany(Booking::class, 'provider_id');
    }

    public function reviewsAsClient()
    {
        return $this->hasMany(Review::class, 'client_id');
    }

    public function reviewsAsProvider()
    {
        return $this->hasMany(Review::class, 'provider_id');
    }
}
