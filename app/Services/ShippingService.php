<?php

namespace App\Services;

use App\Models\ShippingCity;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class ShippingService
{
    public function activeCities(): Collection
    {
        return ShippingCity::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function resolveCity(int $cityId): ShippingCity
    {
        $city = ShippingCity::active()->find($cityId);

        if (! $city) {
            throw new InvalidArgumentException('Selected shipping city is unavailable.');
        }

        return $city;
    }

    public function calculateFee(ShippingCity $city): float
    {
        return $city->shippingFee();
    }

    public function quote(float $subtotal, ShippingCity|int $city): array
    {
        $city = $city instanceof ShippingCity ? $city : $this->resolveCity($city);
        $shipping = $this->calculateFee($city);

        return [
            'subtotal' => round($subtotal, 2),
            'shipping' => $shipping,
            'tax' => 0.0,
            'total' => round($subtotal + $shipping, 2),
            'city' => $city,
        ];
    }
}
