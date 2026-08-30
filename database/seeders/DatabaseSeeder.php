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
            ['email' => 'admin@autoparts.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'phone' => '+923001234567',
                'city' => 'Karachi',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Demo Customer',
                'password' => Hash::make('password'),
                'phone' => '+923009876543',
                'address' => '123 Main Street',
                'city' => 'Lahore',
                'email_verified_at' => now(),
            ]
        );

        $categories = [
            ['name' => 'Engine Parts', 'description' => 'Pistons, gaskets, timing belts and more', 'sort_order' => 1],
            ['name' => 'Brakes & Suspension', 'description' => 'Pads, rotors, shocks and struts', 'sort_order' => 2],
            ['name' => 'Electrical', 'description' => 'Batteries, alternators, sensors', 'sort_order' => 3],
            ['name' => 'Body & Exterior', 'description' => 'Bumpers, mirrors, lights', 'sort_order' => 4],
            ['name' => 'Filters & Fluids', 'description' => 'Oil, air, fuel filters and fluids', 'sort_order' => 5],
            ['name' => 'Accessories', 'description' => 'Floor mats, covers, tools', 'sort_order' => 6],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($cat['name'])],
                array_merge($cat, ['is_active' => true])
            );
        }

        $products = [
            [
                'category' => 'engine-parts',
                'name' => 'Premium Oil Filter',
                'sku' => 'AP-OF-001',
                'brand' => 'Bosch',
                'short_description' => 'High-efficiency oil filter for extended engine life.',
                'description' => 'Premium synthetic media oil filter designed for modern engines. Provides superior filtration and flow rate.',
                'price' => 2500,
                'sale_price' => 1999,
                'stock' => 50,
                'part_number' => 'OF-3323',
                'vehicle_make' => 'Toyota',
                'vehicle_model' => 'Corolla',
                'vehicle_year_from' => '2015',
                'vehicle_year_to' => '2024',
                'warranty' => '1 Year',
                'is_featured' => true,
                'specifications' => ['Thread Size' => 'M20x1.5', 'Height' => '95mm', 'Outer Diameter' => '76mm'],
            ],
            [
                'category' => 'brakes-suspension',
                'name' => 'Ceramic Brake Pads (Front)',
                'sku' => 'AP-BP-002',
                'brand' => 'Brembo',
                'short_description' => 'Low-dust ceramic brake pads with excellent stopping power.',
                'description' => 'OEM-quality ceramic brake pads for front axle. Quiet operation with minimal brake dust.',
                'price' => 8500,
                'stock' => 30,
                'part_number' => 'P85073',
                'vehicle_make' => 'Honda',
                'vehicle_model' => 'Civic',
                'vehicle_year_from' => '2016',
                'vehicle_year_to' => '2023',
                'warranty' => '2 Years',
                'is_featured' => true,
            ],
            [
                'category' => 'electrical',
                'name' => '12V Maintenance-Free Battery',
                'sku' => 'AP-BT-003',
                'brand' => 'Exide',
                'short_description' => '55Ah maintenance-free car battery with 2-year warranty.',
                'description' => 'Reliable 12V battery suitable for sedans and compact SUVs. Calcium technology for longer life.',
                'price' => 18500,
                'sale_price' => 16999,
                'stock' => 20,
                'part_number' => 'EX-55MF',
                'vehicle_make' => 'Universal',
                'warranty' => '2 Years',
                'is_featured' => true,
            ],
            [
                'category' => 'filters-fluids',
                'name' => 'Synthetic Engine Oil 5W-30 (4L)',
                'sku' => 'AP-OIL-004',
                'brand' => 'Castrol',
                'short_description' => 'Full synthetic 5W-30 engine oil for superior protection.',
                'description' => 'Advanced full synthetic formula reduces engine wear and improves fuel efficiency.',
                'price' => 7200,
                'stock' => 100,
                'part_number' => 'EDGE-5W30-4L',
                'warranty' => 'N/A',
            ],
            [
                'category' => 'body-exterior',
                'name' => 'LED Headlight Assembly (Pair)',
                'sku' => 'AP-HL-005',
                'brand' => 'Philips',
                'short_description' => 'Bright LED headlight assembly with DRL.',
                'description' => 'Direct-fit LED headlight replacement with integrated daytime running lights.',
                'price' => 24500,
                'stock' => 15,
                'vehicle_make' => 'Suzuki',
                'vehicle_model' => 'Alto',
                'vehicle_year_from' => '2019',
                'vehicle_year_to' => '2024',
                'is_featured' => true,
            ],
            [
                'category' => 'accessories',
                'name' => 'All-Weather Floor Mats (Set of 4)',
                'sku' => 'AP-ACC-006',
                'brand' => 'WeatherTech',
                'short_description' => 'Custom-fit all-weather floor mats.',
                'description' => 'Heavy-duty rubber floor mats protect your vehicle interior from dirt and spills.',
                'price' => 6500,
                'stock' => 40,
            ],
        ];

        foreach ($products as $item) {
            $category = Category::where('slug', $item['category'])->first();
            unset($item['category']);

            Product::updateOrCreate(
                ['sku' => $item['sku']],
                array_merge($item, [
                    'category_id' => $category->id,
                    'slug' => \Illuminate\Support\Str::slug($item['name']),
                    'condition' => 'new',
                    'is_active' => true,
                ])
            );
        }

        $this->call(MotorcycleCatalogSeeder::class);
    }
}
