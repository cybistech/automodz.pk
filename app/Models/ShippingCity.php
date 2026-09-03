<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCity extends Model
{
    protected $fillable = [
        'name',
        'distance_km',
        'base_fee',
        'rate_per_km',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'distance_km' => 'decimal:2',
            'base_fee' => 'decimal:2',
            'rate_per_km' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function shippingFee(): float
    {
        return round((float) $this->base_fee + ((float) $this->distance_km * (float) $this->rate_per_km), 2);
    }
}
