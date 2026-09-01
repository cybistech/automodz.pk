<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MotorcycleCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureStorageDirectories();

        $categories = $this->seedCategories();
        $this->seedProducts($categories);
    }

    private function ensureStorageDirectories(): void
    {
        Storage::disk('public')->makeDirectory('products');
    }

    private function seedCategories(): array
    {
        $data = [
            'motorcycle-lighting' => [
                'name' => 'Motorcycle Lighting',
                'description' => 'DRL lights, fog lights, indicators, and flasher relays for motorcycles and scooters.',
                'meta_title' => 'Motorcycle Lighting Parts Pakistan | DRL, Fog & Indicator Lights',
                'meta_description' => 'Shop motorcycle DRL lights, fog lamps, LED indicators and flasher relays in Pakistan. Best prices in PKR with fast delivery.',
                'meta_keywords' => 'motorcycle lights, DRL lights Pakistan, fog lights bike, LED indicators, indicator flasher',
                'sort_order' => 1,
            ],
            'electrical-charging' => [
                'name' => 'Electrical & Charging',
                'description' => 'USB chargers and electrical accessories for motorcycles.',
                'meta_title' => 'Motorcycle USB Charger Pakistan | 12V Bike Charging Accessories',
                'meta_description' => 'Buy waterproof motorcycle USB chargers and 12V charging accessories online in Pakistan at discounted PKR prices.',
                'meta_keywords' => 'motorcycle USB charger, bike phone charger, 12V USB adapter Pakistan',
                'sort_order' => 2,
            ],
            'mounts-holders' => [
                'name' => 'Mounts & Holders',
                'description' => 'Mobile holders and waterproof mounts for safe riding.',
                'meta_title' => 'Motorcycle Mobile Holder Pakistan | Waterproof Phone Mounts',
                'meta_description' => 'Anti-vibration motorcycle mobile holders and waterproof phone mounts. Secure your phone while riding. Sale prices in PKR.',
                'meta_keywords' => 'motorcycle mobile holder, waterproof phone mount bike, anti shake holder Pakistan',
                'sort_order' => 3,
            ],
            'body-protection' => [
                'name' => 'Body & Protection',
                'description' => 'Tank pads, mirrors, locks, and body accessories.',
                'meta_title' => 'Motorcycle Body Parts Pakistan | Tank Pads, Mirrors & Locks',
                'meta_description' => 'Motorcycle tank pads, side mirrors, side locks and body protection accessories at sale prices in Pakistan.',
                'meta_keywords' => 'motorcycle tank pad, bike side mirror, side lock, body accessories Pakistan',
                'sort_order' => 4,
            ],
            'controls-accessories' => [
                'name' => 'Controls & Accessories',
                'description' => 'Gear levers, keychains, and riding accessories.',
                'meta_title' => 'Motorcycle Controls & Accessories Pakistan | Gear Levers & Keychains',
                'meta_description' => 'Universal gear levers, stylish keychains and motorcycle control accessories. Affordable PKR prices with offers.',
                'meta_keywords' => 'gear lever motorcycle, bike keychain, riding accessories Pakistan',
                'sort_order' => 5,
            ],
        ];

        $categories = [];
        foreach ($data as $slug => $item) {
            $categories[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                array_merge($item, ['is_active' => true])
            );
        }

        return $categories;
    }

    private function seedProducts(array $categories): void
    {
        $products = [
            [
                'sku' => 'MC-USB-001',
                'category' => 'electrical-charging',
                'name' => 'Motorcycle USB Charger 12V Dual Port',
                'brand' => 'RideCharge',
                'price' => 600,
                'sale_price' => 350,
                'stock' => 85,
                'is_featured' => true,
                'part_number' => 'RC-USB-12V',
                'vehicle_make' => 'Universal',
                'warranty' => '6 Months',
                'short_description' => 'Water-resistant dual USB charger for motorcycles. Fast charging on the go.',
                'description' => "Keep your devices powered on every ride with our Motorcycle USB Charger 12V Dual Port. Designed for bikes and scooters, this compact charger mounts easily on handlebars or near the ignition switch.\n\nFeatures waterproof housing, dual USB outputs, and built-in overload protection. Ideal for navigation, music, and emergency calls while riding across Pakistan.",
                'meta_title' => 'Motorcycle USB Charger 12V Dual Port | Sale Rs. 350 PKR',
                'meta_description' => 'Buy motorcycle USB charger at Rs. 350 (was Rs. 600). Waterproof dual-port 12V bike charger with fast delivery across Pakistan.',
                'meta_keywords' => 'motorcycle USB charger, bike charger Pakistan, 12V USB port, dual USB motorcycle',
                'specifications' => [
                    'Voltage' => '12V DC',
                    'USB Ports' => '2',
                    'Output' => '5V / 2.1A per port',
                    'Water Resistance' => 'IP65',
                    'Mount Type' => 'Handlebar / Panel',
                    'Cable Length' => '1.2m',
                ],
                'colors' => ['#1e3a5f', '#2563eb', '#0f172a'],
            ],
            [
                'sku' => 'MC-FLS-002',
                'category' => 'motorcycle-lighting',
                'name' => 'Motorcycle Indicator Flasher Relay',
                'brand' => 'SignalPro',
                'price' => 150,
                'sale_price' => 120,
                'stock' => 120,
                'part_number' => 'SP-FLS-12V',
                'vehicle_make' => 'Universal',
                'warranty' => '3 Months',
                'short_description' => 'Heavy-duty indicator flasher relay for smooth turn signal operation.',
                'description' => "Replace worn-out flashers with this reliable Motorcycle Indicator Flasher Relay. Ensures consistent blinking speed for LED and standard indicators on most motorcycles and scooters.\n\nEasy plug-and-play installation with universal 12V compatibility.",
                'meta_title' => 'Motorcycle Indicator Flasher Relay | Rs. 120 PKR Sale',
                'meta_description' => 'Universal motorcycle indicator flasher relay at Rs. 120. Fix fast or slow turn signals. Compatible with most bikes in Pakistan.',
                'meta_keywords' => 'indicator flasher relay, motorcycle flasher, turn signal relay Pakistan',
                'specifications' => [
                    'Voltage' => '12V',
                    'Type' => 'Electronic Flasher',
                    'Pins' => '3-Pin',
                    'Flash Rate' => '60-120 flashes/min',
                    'Compatibility' => 'LED & Halogen',
                ],
                'colors' => ['#422006', '#f59e0b', '#1c1917'],
            ],
            [
                'sku' => 'MC-GLV-003',
                'category' => 'controls-accessories',
                'name' => 'Universal Motorcycle Gear Lever',
                'brand' => 'ShiftMax',
                'price' => 200,
                'sale_price' => 165,
                'stock' => 60,
                'part_number' => 'SM-GLV-UNI',
                'vehicle_make' => 'Universal',
                'warranty' => '6 Months',
                'short_description' => 'Durable universal gear lever with improved grip and control.',
                'description' => "Upgrade shifting comfort with our Universal Motorcycle Gear Lever. Forged aluminum construction with anti-slip tip for confident gear changes in all weather.\n\nFits most commuter and sport motorcycles with adjustable length options.",
                'meta_title' => 'Universal Motorcycle Gear Lever | Rs. 165 PKR Offer',
                'meta_description' => 'Universal bike gear lever on sale at Rs. 165. Strong aluminum build, better grip, fits most motorcycles in Pakistan.',
                'meta_keywords' => 'motorcycle gear lever, bike gear shifter, universal gear lever Pakistan',
                'specifications' => [
                    'Material' => 'Forged Aluminum',
                    'Finish' => 'Anodized Black',
                    'Length' => 'Adjustable',
                    'Compatibility' => 'Universal Motorcycle',
                    'Tip' => 'Anti-Slip Rubber',
                ],
                'colors' => ['#18181b', '#52525b', '#27272a'],
            ],
            [
                'sku' => 'MC-DRL-004P',
                'category' => 'motorcycle-lighting',
                'name' => 'DRL LED Lights Premium (Pair)',
                'brand' => 'BrightRide',
                'price' => 2400,
                'sale_price' => 1650,
                'stock' => 35,
                'is_featured' => true,
                'part_number' => 'BR-DRL-P',
                'vehicle_make' => 'Universal',
                'warranty' => '1 Year',
                'short_description' => 'Premium high-output DRL LED lights for maximum daytime visibility.',
                'description' => "Premium DRL LED Lights deliver powerful white beam for superior daytime visibility and a modern look. Weather-sealed housing handles rain, dust, and highway speeds.\n\nIncludes mounting brackets and wiring harness for easy installation on motorcycles and scooters.",
                'meta_title' => 'Premium DRL LED Lights for Motorcycle | Rs. 1650 PKR Sale',
                'meta_description' => 'Premium motorcycle DRL LED lights now Rs. 1650 (was Rs. 2400). Bright daytime running lights with 1-year warranty.',
                'meta_keywords' => 'DRL lights motorcycle, LED daytime running lights, premium DRL Pakistan',
                'specifications' => [
                    'Type' => 'LED DRL',
                    'Quantity' => 'Pair (2 pcs)',
                    'Power' => '12W each',
                    'Color Temperature' => '6000K White',
                    'Voltage' => '12V',
                    'Waterproof' => 'IP67',
                ],
                'colors' => ['#0c4a6e', '#38bdf8', '#082f49'],
            ],
            [
                'sku' => 'MC-DRL-004S',
                'category' => 'motorcycle-lighting',
                'name' => 'DRL LED Lights Standard (Pair)',
                'brand' => 'BrightRide',
                'price' => 1650,
                'sale_price' => 1450,
                'stock' => 50,
                'part_number' => 'BR-DRL-S',
                'vehicle_make' => 'Universal',
                'warranty' => '6 Months',
                'short_description' => 'Standard DRL LED lights with excellent visibility at a great price.',
                'description' => "Our Standard DRL LED Lights offer reliable daytime visibility without breaking the bank. Slim profile design suits most motorcycle front fairings and crash guards.\n\nEnergy-efficient LEDs with long service life.",
                'meta_title' => 'Standard DRL LED Lights Motorcycle | Rs. 1450 PKR',
                'meta_description' => 'Standard DRL LED lights for bikes at Rs. 1450. Slim design, bright white LEDs, easy install. On sale now in Pakistan.',
                'meta_keywords' => 'standard DRL lights, affordable DRL motorcycle, LED running lights Pakistan',
                'specifications' => [
                    'Type' => 'LED DRL',
                    'Quantity' => 'Pair (2 pcs)',
                    'Power' => '8W each',
                    'Color Temperature' => '5500K White',
                    'Voltage' => '12V',
                    'Waterproof' => 'IP65',
                ],
                'colors' => ['#1e40af', '#60a5fa', '#172554'],
            ],
            [
                'sku' => 'MC-FOG-005B',
                'category' => 'motorcycle-lighting',
                'name' => 'Fog Lights Budget (Pair)',
                'brand' => 'NightBeam',
                'price' => 450,
                'sale_price' => 320,
                'stock' => 70,
                'part_number' => 'NB-FOG-B',
                'vehicle_make' => 'Universal',
                'warranty' => '3 Months',
                'short_description' => 'Affordable fog lights for improved visibility in rain and fog.',
                'description' => "Budget-friendly Fog Lights improve visibility during rain, fog, and night rides. Compact design fits most motorcycle front mounts.\n\nGreat value option for daily commuters.",
                'meta_title' => 'Budget Motorcycle Fog Lights | Rs. 320 PKR Sale',
                'meta_description' => 'Affordable motorcycle fog lights at Rs. 320. Better visibility in rain and fog. Universal fit for bikes in Pakistan.',
                'meta_keywords' => 'budget fog lights, motorcycle fog lamp, cheap fog lights Pakistan',
                'specifications' => [
                    'Type' => 'Halogen Fog Light',
                    'Quantity' => 'Pair',
                    'Power' => '35W each',
                    'Beam' => 'Wide Flood',
                    'Voltage' => '12V',
                    'Housing' => 'ABS Plastic',
                ],
                'colors' => ['#374151', '#9ca3af', '#111827'],
            ],
            [
                'sku' => 'MC-FOG-005S',
                'category' => 'motorcycle-lighting',
                'name' => 'Fog Lights Standard (Pair)',
                'brand' => 'NightBeam',
                'price' => 1050,
                'sale_price' => 800,
                'stock' => 45,
                'is_featured' => true,
                'part_number' => 'NB-FOG-S',
                'vehicle_make' => 'Universal',
                'warranty' => '6 Months',
                'short_description' => 'Standard fog lights with bright output and durable housing.',
                'description' => "Standard Fog Lights deliver a strong, focused beam for challenging weather conditions. Metal housing with glass lens for durability on long rides.\n\nIncludes wiring kit and switch.",
                'meta_title' => 'Standard Motorcycle Fog Lights | Rs. 800 PKR Offer',
                'meta_description' => 'Motorcycle fog lights standard pair at Rs. 800 (was Rs. 1050). Bright beam, metal housing, includes wiring kit.',
                'meta_keywords' => 'motorcycle fog lights, standard fog lamp bike, fog light pair Pakistan',
                'specifications' => [
                    'Type' => 'Halogen Fog Light',
                    'Quantity' => 'Pair',
                    'Power' => '55W each',
                    'Lens' => 'Glass',
                    'Housing' => 'Metal',
                    'Voltage' => '12V',
                ],
                'colors' => ['#44403c', '#d6d3d1', '#1c1917'],
            ],
            [
                'sku' => 'MC-FOG-005P',
                'category' => 'motorcycle-lighting',
                'name' => 'Fog Lights Premium LED (Pair)',
                'brand' => 'NightBeam',
                'price' => 1300,
                'sale_price' => 1050,
                'stock' => 30,
                'part_number' => 'NB-FOG-P',
                'vehicle_make' => 'Universal',
                'warranty' => '1 Year',
                'short_description' => 'Premium LED fog lights with powerful output and long lifespan.',
                'description' => "Premium LED Fog Lights provide maximum visibility with low power draw. Projector-style lens cuts through fog and rain effectively.\n\nTop choice for touring and highway riders.",
                'meta_title' => 'Premium LED Fog Lights Motorcycle | Rs. 1050 PKR',
                'meta_description' => 'Premium LED fog lights for motorcycles at Rs. 1050. Projector lens, IP67 waterproof, 1-year warranty.',
                'meta_keywords' => 'LED fog lights motorcycle, premium fog lamp, projector fog lights Pakistan',
                'specifications' => [
                    'Type' => 'LED Fog Light',
                    'Quantity' => 'Pair',
                    'Power' => '20W each',
                    'Lens' => 'Projector',
                    'Waterproof' => 'IP67',
                    'Voltage' => '12V',
                ],
                'colors' => ['#0f766e', '#5eead4', '#134e4a'],
            ],
            [
                'sku' => 'MC-TNK-006',
                'category' => 'body-protection',
                'name' => 'Motorcycle Tank Pad Protector',
                'brand' => 'GripGuard',
                'price' => 350,
                'sale_price' => 280,
                'stock' => 90,
                'part_number' => 'GG-TNK-01',
                'vehicle_make' => 'Universal',
                'warranty' => '6 Months',
                'short_description' => 'Anti-scratch tank pad that protects paint and improves rider grip.',
                'description' => "Protect your fuel tank from scratches, belt buckles, and tank bag wear with our Motorcycle Tank Pad Protector. Gel construction absorbs vibration and provides knee grip during cornering.\n\nUniversal design trims to fit most tank shapes.",
                'meta_title' => 'Motorcycle Tank Pad Protector | Rs. 280 PKR Sale',
                'meta_description' => 'Motorcycle tank pad protector at Rs. 280. Anti-scratch, improves knee grip, universal fit. Order online in Pakistan.',
                'meta_keywords' => 'motorcycle tank pad, fuel tank protector, anti scratch tank pad Pakistan',
                'specifications' => [
                    'Material' => 'Gel / Rubber Composite',
                    'Thickness' => '3mm',
                    'Finish' => 'Carbon Fiber Pattern',
                    'Adhesive' => '3M Strong Bond',
                    'Fitment' => 'Universal Trim-to-Fit',
                ],
                'colors' => ['#1f2937', '#6b7280', '#111827'],
            ],
            [
                'sku' => 'MC-MH-007',
                'category' => 'mounts-holders',
                'name' => 'Motorcycle Mobile Holder Anti-Vibration',
                'brand' => 'RideMount',
                'price' => 700,
                'sale_price' => 550,
                'stock' => 75,
                'is_featured' => true,
                'part_number' => 'RM-MH-01',
                'vehicle_make' => 'Universal',
                'warranty' => '6 Months',
                'short_description' => 'Sturdy anti-vibration mobile holder for handlebar mounting.',
                'description' => "Navigate safely with our Motorcycle Mobile Holder. Anti-vibration dampener keeps your screen stable on rough roads. One-hand quick release for easy phone access.\n\nFits phones 4.7\" to 6.8\" with or without case.",
                'meta_title' => 'Motorcycle Mobile Holder Anti-Vibration | Rs. 550 PKR',
                'meta_description' => 'Anti-vibration motorcycle mobile holder at Rs. 550. Handlebar mount, fits all phones, quick release. Sale price in PKR.',
                'meta_keywords' => 'motorcycle mobile holder, bike phone mount, anti vibration holder Pakistan',
                'specifications' => [
                    'Mount' => 'Handlebar Clamp',
                    'Phone Size' => '4.7" - 6.8"',
                    'Rotation' => '360°',
                    'Vibration Dampening' => 'Yes',
                    'Material' => 'ABS + Silicone Grip',
                ],
                'colors' => ['#7c2d12', '#fb923c', '#292524'],
            ],
            [
                'sku' => 'MC-WPH-008',
                'category' => 'mounts-holders',
                'name' => 'Waterproof Mobile Holder Pouch',
                'brand' => 'RideMount',
                'price' => 700,
                'sale_price' => 550,
                'stock' => 65,
                'part_number' => 'RM-WPH-01',
                'vehicle_make' => 'Universal',
                'warranty' => '6 Months',
                'short_description' => 'Waterproof touch-sensitive pouch for phones in all weather.',
                'description' => "Ride in rain with confidence using our Waterproof Mobile Holder Pouch. Touch-sensitive clear window lets you use maps and answer calls without removing your phone.\n\nUniversal handlebar mount with sealed zipper closure.",
                'meta_title' => 'Waterproof Motorcycle Mobile Holder | Rs. 550 PKR Sale',
                'meta_description' => 'Waterproof bike phone pouch at Rs. 550. Touch screen compatible, handlebar mount, rain-proof riding in Pakistan.',
                'meta_keywords' => 'waterproof mobile holder motorcycle, rain proof phone pouch bike',
                'specifications' => [
                    'Waterproof Rating' => 'IPX6',
                    'Touch Screen' => 'Yes',
                    'Max Phone Size' => '7 inches',
                    'Mount' => 'Handlebar',
                    'Closure' => 'Sealed Zipper',
                ],
                'colors' => ['#1d4ed8', '#93c5fd', '#0f172a'],
            ],
            [
                'sku' => 'MC-IND-009P',
                'category' => 'motorcycle-lighting',
                'name' => 'LED Turn Indicators (Pair)',
                'brand' => 'SignalPro',
                'price' => 200,
                'sale_price' => 155,
                'stock' => 100,
                'part_number' => 'SP-IND-2',
                'vehicle_make' => 'Universal',
                'warranty' => '6 Months',
                'short_description' => 'Bright LED turn indicators sold as a pair (2 pieces).',
                'description' => "Upgrade to bright LED Turn Indicators (Pair). Low power consumption with high visibility amber lenses. Direct replacement for most motorcycle indicator mounts.\n\nSold as set of 2 indicators (left + right).",
                'meta_title' => 'LED Turn Indicators Pair Motorcycle | Rs. 155 PKR',
                'meta_description' => 'LED motorcycle indicators pair at Rs. 155. Bright amber turn signals, universal fit, set of 2 pieces.',
                'meta_keywords' => 'LED indicators motorcycle, turn signals pair, bike indicators Pakistan',
                'specifications' => [
                    'Type' => 'LED Indicator',
                    'Quantity' => '2 (Pair)',
                    'Color' => 'Amber',
                    'Voltage' => '12V',
                    'Lens' => 'Smoked / Clear Options',
                ],
                'colors' => ['#ca8a04', '#fde047', '#422006'],
            ],
            [
                'sku' => 'MC-IND-009Q',
                'category' => 'motorcycle-lighting',
                'name' => 'LED Turn Indicators (Set of 4)',
                'brand' => 'SignalPro',
                'price' => 750,
                'sale_price' => 600,
                'stock' => 55,
                'part_number' => 'SP-IND-4',
                'vehicle_make' => 'Universal',
                'warranty' => '6 Months',
                'short_description' => 'Complete set of 4 LED indicators for front and rear.',
                'description' => "Complete your lighting upgrade with LED Turn Indicators Set of 4. Includes front and rear indicators for full bike coverage. Energy-efficient LEDs with uniform blink pattern.\n\nBest value pack for full replacement.",
                'meta_title' => 'LED Indicators Set of 4 Motorcycle | Rs. 600 PKR Offer',
                'meta_description' => 'LED indicator set of 4 for motorcycles at Rs. 600. Front and rear turn signals, universal 12V fit.',
                'meta_keywords' => 'LED indicators set of 4, motorcycle turn signals full set Pakistan',
                'specifications' => [
                    'Type' => 'LED Indicator',
                    'Quantity' => '4 (Full Set)',
                    'Color' => 'Amber',
                    'Voltage' => '12V',
                    'Includes' => 'Front + Rear',
                ],
                'colors' => ['#a16207', '#fbbf24', '#451a03'],
            ],
            [
                'sku' => 'MC-MIR-010S',
                'category' => 'body-protection',
                'name' => 'Motorcycle Side Mirror Standard',
                'brand' => 'ClearView',
                'price' => 800,
                'sale_price' => 650,
                'stock' => 40,
                'part_number' => 'CV-MIR-S',
                'vehicle_make' => 'Universal',
                'warranty' => '6 Months',
                'short_description' => 'Standard side mirror with wide-angle glass and universal mount.',
                'description' => "Standard Motorcycle Side Mirror provides clear rear visibility with wide-angle glass. Universal 10mm / 8mm thread adapters included for most bikes.\n\nVibration-resistant design for stable view at highway speeds.",
                'meta_title' => 'Motorcycle Side Mirror Standard | Rs. 650 PKR Sale',
                'meta_description' => 'Standard motorcycle side mirror at Rs. 650. Wide-angle glass, universal mount, vibration resistant.',
                'meta_keywords' => 'motorcycle side mirror, bike mirror standard, rear view mirror Pakistan',
                'specifications' => [
                    'Type' => 'Side Mirror',
                    'Glass' => 'Wide Angle',
                    'Thread' => '8mm / 10mm Adapters',
                    'Housing' => 'ABS',
                    'Quantity' => '1 piece',
                ],
                'colors' => ['#334155', '#94a3b8', '#0f172a'],
            ],
            [
                'sku' => 'MC-MIR-010P',
                'category' => 'body-protection',
                'name' => 'Motorcycle Side Mirror Premium',
                'brand' => 'ClearView',
                'price' => 1600,
                'sale_price' => 1300,
                'stock' => 25,
                'is_featured' => true,
                'part_number' => 'CV-MIR-P',
                'vehicle_make' => 'Universal',
                'warranty' => '1 Year',
                'short_description' => 'Premium aluminum side mirror with anti-glare blue glass.',
                'description' => "Premium Motorcycle Side Mirror features CNC aluminum stem, anti-glare blue glass, and foldable design. Stylish look for sport and touring motorcycles.\n\nSuperior build quality with 1-year warranty.",
                'meta_title' => 'Premium Motorcycle Side Mirror | Rs. 1300 PKR Offer',
                'meta_description' => 'Premium bike side mirror at Rs. 1300. Aluminum stem, anti-glare glass, foldable design. Top quality in Pakistan.',
                'meta_keywords' => 'premium motorcycle mirror, aluminum side mirror, anti glare mirror Pakistan',
                'specifications' => [
                    'Type' => 'Side Mirror',
                    'Stem Material' => 'CNC Aluminum',
                    'Glass' => 'Anti-Glare Blue',
                    'Foldable' => 'Yes',
                    'Thread' => '10mm Universal',
                ],
                'colors' => ['#57534e', '#d6d3d1', '#1c1917'],
            ],
            [
                'sku' => 'MC-LCK-011',
                'category' => 'body-protection',
                'name' => 'Motorcycle Side Lock Set',
                'brand' => 'SecureRide',
                'price' => 550,
                'sale_price' => 450,
                'stock' => 50,
                'part_number' => 'SR-SLK-01',
                'vehicle_make' => 'Universal',
                'warranty' => '1 Year',
                'short_description' => 'Heavy-duty side lock set for motorcycle security.',
                'description' => "Protect your motorcycle with our Side Lock Set. Hardened steel construction resists cutting and prying. Includes 2 keys and weather-resistant cap.\n\nCompact design fits under seat or mounts to frame.",
                'meta_title' => 'Motorcycle Side Lock Set | Rs. 450 PKR Sale',
                'meta_description' => 'Motorcycle side lock set at Rs. 450. Hardened steel, 2 keys included, anti-theft security for bikes in Pakistan.',
                'meta_keywords' => 'motorcycle side lock, bike lock set, anti theft lock Pakistan',
                'specifications' => [
                    'Material' => 'Hardened Steel',
                    'Keys Included' => '2',
                    'Lock Type' => 'Disc / Side Lock',
                    'Weather Cap' => 'Yes',
                    'Weight' => '450g',
                ],
                'colors' => ['#3f3f46', '#a1a1aa', '#18181b'],
            ],
            [
                'sku' => 'MC-KEY-012B',
                'category' => 'controls-accessories',
                'name' => 'Motorcycle Keychain Basic',
                'brand' => 'RideStyle',
                'price' => 80,
                'sale_price' => 50,
                'stock' => 200,
                'part_number' => 'RS-KEY-B',
                'vehicle_make' => 'Universal',
                'warranty' => 'N/A',
                'short_description' => 'Simple metal motorcycle keychain with logo tag.',
                'description' => "Stylish Motorcycle Keychain Basic with metal tag and durable ring. Perfect gift for riders. Lightweight and scratch-resistant finish.",
                'meta_title' => 'Motorcycle Keychain Basic | Rs. 50 PKR',
                'meta_description' => 'Basic motorcycle keychain at Rs. 50. Metal tag, durable ring, great rider gift. Buy online in Pakistan.',
                'meta_keywords' => 'motorcycle keychain, bike keyring, cheap keychain Pakistan',
                'specifications' => [
                    'Material' => 'Zinc Alloy',
                    'Ring Type' => 'Split Ring',
                    'Finish' => 'Matte Black',
                    'Weight' => '15g',
                ],
                'colors' => ['#292524', '#78716c', '#1c1917'],
            ],
            [
                'sku' => 'MC-KEY-012S',
                'category' => 'controls-accessories',
                'name' => 'Motorcycle Keychain Premium',
                'brand' => 'RideStyle',
                'price' => 150,
                'sale_price' => 100,
                'stock' => 150,
                'part_number' => 'RS-KEY-S',
                'vehicle_make' => 'Universal',
                'warranty' => 'N/A',
                'short_description' => 'Premium leather-wrapped motorcycle keychain.',
                'description' => "Premium Motorcycle Keychain with genuine leather wrap and chrome accent. Elegant design for everyday carry. Makes a great gift for motorcycle enthusiasts.",
                'meta_title' => 'Premium Motorcycle Keychain | Rs. 100 PKR Sale',
                'meta_description' => 'Premium leather motorcycle keychain at Rs. 100. Chrome accent, stylish rider accessory in Pakistan.',
                'meta_keywords' => 'premium keychain motorcycle, leather keyring bike',
                'specifications' => [
                    'Material' => 'Leather + Chrome',
                    'Ring Type' => 'Heavy Duty',
                    'Finish' => 'Leather Wrap',
                    'Weight' => '25g',
                ],
                'colors' => ['#78350f', '#fbbf24', '#451a03'],
            ],
            [
                'sku' => 'MC-KEY-012L',
                'category' => 'controls-accessories',
                'name' => 'Motorcycle Keychain LED',
                'brand' => 'RideStyle',
                'price' => 280,
                'sale_price' => 220,
                'stock' => 80,
                'part_number' => 'RS-KEY-L',
                'vehicle_make' => 'Universal',
                'warranty' => '3 Months',
                'short_description' => 'LED flashlight keychain for riders — find keyholes in the dark.',
                'description' => "Motorcycle Keychain LED combines style with function. Built-in mini LED flashlight helps find keyholes and inspect your bike at night. Replaceable battery included.",
                'meta_title' => 'Motorcycle LED Keychain Flashlight | Rs. 220 PKR',
                'meta_description' => 'LED motorcycle keychain at Rs. 220. Built-in flashlight, metal body, practical rider accessory in PKR.',
                'meta_keywords' => 'LED keychain motorcycle, flashlight keyring bike',
                'specifications' => [
                    'Material' => 'Aluminum',
                    'LED' => 'White Mini LED',
                    'Battery' => 'Replaceable CR2016',
                    'Weight' => '30g',
                ],
                'colors' => ['#14532d', '#4ade80', '#052e16'],
            ],
        ];

        foreach ($products as $item) {
            $colors = $item['colors'];
            unset($item['colors']);

            $slug = Str::slug($item['name']);
            $images = $this->generateProductImages($slug, $item['name'], $colors);

            Product::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'category_id' => $categories[$item['category']]->id,
                    'name' => $item['name'],
                    'slug' => $slug,
                    'brand' => $item['brand'],
                    'short_description' => $item['short_description'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'sale_price' => $item['sale_price'],
                    'stock' => $item['stock'],
                    'condition' => 'new',
                    'part_number' => $item['part_number'],
                    'vehicle_make' => $item['vehicle_make'],
                    'warranty' => $item['warranty'],
                    'specifications' => $item['specifications'],
                    'images' => $images,
                    'meta_title' => $item['meta_title'],
                    'meta_description' => $item['meta_description'],
                    'meta_keywords' => $item['meta_keywords'],
                    'is_featured' => $item['is_featured'] ?? false,
                    'is_active' => true,
                ]
            );
        }
    }

  private function generateProductImages(string $slug, string $name, array $colors): array
    {
        $images = [];
        $labels = ['Main View', 'Detail View', 'Package View'];

        foreach ($labels as $index => $label) {
            $filename = "products/{$slug}-{$index}.jpg";
            $this->createProductImage(
                Storage::disk('public')->path($filename),
                $name,
                $label,
                $colors[$index % count($colors)]
            );
            $images[] = $filename;
        }

        return $images;
    }

    private function createProductImage(string $path, string $name, string $label, string $hexColor): void
    {
        $width = 800;
        $height = 800;
        $image = imagecreatetruecolor($width, $height);

        [$r, $g, $b] = $this->hexToRgb($hexColor);
        $bg = imagecolorallocate($image, max(0, $r - 30), max(0, $g - 30), max(0, $b - 30));
        $accent = imagecolorallocate($image, $r, $g, $b);
        $white = imagecolorallocate($image, 255, 255, 255);
        $light = imagecolorallocatealpha($image, 255, 255, 255, 90);

        imagefilledrectangle($image, 0, 0, $width, $height, $bg);
        imagefilledellipse($image, 400, 320, 420, 420, $accent);
        imagefilledellipse($image, 340, 260, 180, 180, $light);

        $font = 5;
        $wrapped = wordwrap($name, 22, "\n");
        $lines = explode("\n", $wrapped);
        $y = 520;
        foreach ($lines as $line) {
            $tw = imagefontwidth($font) * strlen($line);
            imagestring($image, $font, (int) (($width - $tw) / 2), $y, $line, $white);
            $y += 18;
        }

        $lw = imagefontwidth($font) * strlen($label);
        imagestring($image, $font, (int) (($width - $lw) / 2), 620, $label, $white);
        imagestring($image, 3, 20, 20, 'AutoModz', $white);

        imagejpeg($image, $path, 90);
        imagedestroy($image);
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
