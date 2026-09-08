<?php

namespace Database\Seeders;

use App\Models\ShippingCity;
use Illuminate\Database\Seeder;

class ShippingCitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Lahore', 'distance_km' => 0, 'base_fee' => 250, 'rate_per_km' => 0, 'sort_order' => 1],
            ['name' => 'Faisalabad', 'distance_km' => 120, 'base_fee' => 280, 'rate_per_km' => 0, 'sort_order' => 2],
            ['name' => 'Karachi', 'distance_km' => 1200, 'base_fee' => 350, 'rate_per_km' => 0, 'sort_order' => 3],
            ['name' => 'Hyderabad', 'distance_km' => 1050, 'base_fee' => 340, 'rate_per_km' => 0, 'sort_order' => 4],
            ['name' => 'Islamabad', 'distance_km' => 380, 'base_fee' => 300, 'rate_per_km' => 0, 'sort_order' => 5],
            ['name' => 'Rawalpindi', 'distance_km' => 375, 'base_fee' => 300, 'rate_per_km' => 0, 'sort_order' => 6],
            ['name' => 'Multan', 'distance_km' => 350, 'base_fee' => 290, 'rate_per_km' => 0, 'sort_order' => 7],
            ['name' => 'Peshawar', 'distance_km' => 520, 'base_fee' => 320, 'rate_per_km' => 0, 'sort_order' => 8],
            ['name' => 'Quetta', 'distance_km' => 780, 'base_fee' => 380, 'rate_per_km' => 0, 'sort_order' => 9],
        ];

        foreach ($cities as $city) {
            ShippingCity::updateOrCreate(
                ['name' => $city['name']],
                array_merge($city, ['is_active' => true])
            );
        }
    }
}
