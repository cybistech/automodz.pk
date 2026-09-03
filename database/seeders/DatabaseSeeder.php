<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@automodz.pk'],
            [
                'name' => 'AutoModz Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'phone' => '+923001234567',
                'city' => 'Karachi',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@automodz.pk'],
            [
                'name' => 'Demo Rider',
                'password' => Hash::make('password'),
                'phone' => '+923009876543',
                'address' => '123 Main Street',
                'city' => 'Lahore',
                'email_verified_at' => now(),
            ]
        );

        // Deactivate legacy auto-parts catalog
        Category::whereNotIn('slug', [
            'motorcycle-lighting',
            'electrical-charging',
            'mounts-holders',
            'body-protection',
            'controls-accessories',
        ])->update(['is_active' => false]);

        Product::whereHas('category', fn ($q) => $q->where('is_active', false))
            ->update(['is_active' => false]);

        $this->call(ShippingCitySeeder::class);
        $this->call(MotorcycleCatalogSeeder::class);
    }
}
