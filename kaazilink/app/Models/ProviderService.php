<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Data\ServiceDirectory;

class ProviderService extends Model
{
    protected $fillable = [
        'user_id',
        'service_slug',
        'custom_name',
        'custom_description',
        'price',
        'is_custom',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_custom' => 'boolean',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_custom && !empty($this->custom_name)) {
            return $this->custom_name;
        }

        $allServices = collect(ServiceDirectory::getServices());
        $std = $allServices->firstWhere('slug', $this->service_slug);
        return $std ? $std['name'] : ucfirst(str_replace('-', ' ', $this->service_slug));
    }

    public function getDisplayDescriptionAttribute(): string
    {
        if ($this->is_custom && !empty($this->custom_description)) {
            return $this->custom_description;
        }

        return "Professional {$this->display_name} services in Nairobi.";
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price !== null && $this->price > 0) {
            return 'KES ' . number_format($this->price, 0);
        }

        $allServices = collect(ServiceDirectory::getServices());
        $std = $allServices->firstWhere('slug', $this->service_slug);
        return $std ? $std['price_range'] : 'Contact for pricing';
    }
}
