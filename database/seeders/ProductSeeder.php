<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Keyboard' => [
                [
                    'name' => 'Logitech G Pro X TKL',
                    'description' => 'Mechanical gaming keyboard with swappable switches.',
                    'price' => 1999000.00,
                    'stock' => 15,
                    'image_url' => 'https://example.com/images/gprox.jpg',
                    'switch_type' => 'GX Blue Clicky',
                    'connectivity' => 'Wired',
                    'weight' => '980g'
                ],
                [
                    'name' => 'Razer BlackWidow V4 Pro',
                    'description' => 'Full-size mechanical keyboard with Razer Chroma RGB.',
                    'price' => 3200000.00,
                    'stock' => 8,
                    'image_url' => 'https://example.com/images/blackwidow.jpg',
                    'switch_type' => 'Razer Green Mechanical',
                    'connectivity' => 'Wired',
                    'weight' => '1120g'
                ],
                [
                    'name' => 'Keychron Q1 HE',
                    'description' => 'Magnetic Switch mechanical keyboard, extremely customizable.',
                    'price' => 3500000.00,
                    'stock' => 5,
                    'image_url' => 'https://example.com/images/q1he.jpg',
                    'switch_type' => 'Gateron Magnetic',
                    'connectivity' => 'Wired/Wireless',
                    'weight' => '1500g'
                ],
            ],
            'Mouse' => [
                [
                    'name' => 'Logitech G Pro X Superlight 2',
                    'description' => 'Ultra-lightweight wireless gaming mouse.',
                    'price' => 2199000.00,
                    'stock' => 20,
                    'image_url' => 'https://example.com/images/superlight.jpg',
                    'dpi' => 32000,
                    'connectivity' => 'Wireless (LIGHTSPEED)',
                    'sensor' => 'HERO 2',
                    'weight' => '60g'
                ],
                [
                    'name' => 'Razer DeathAdder V3 Pro',
                    'description' => 'Ergonomic wireless gaming mouse for esports.',
                    'price' => 2350000.00,
                    'stock' => 12,
                    'image_url' => 'https://example.com/images/deathadder.jpg',
                    'dpi' => 30000,
                    'connectivity' => 'Wireless (HyperSpeed)',
                    'sensor' => 'Focus Pro 30K Optical',
                    'weight' => '63g'
                ],
                [
                    'name' => 'Finalmouse UltralightX',
                    'description' => 'The lightest carbon fiber gaming mouse.',
                    'price' => 4500000.00,
                    'stock' => 3,
                    'image_url' => 'https://example.com/images/ulx.jpg',
                    'dpi' => 26000,
                    'connectivity' => 'Wireless',
                    'sensor' => 'PAW3395',
                    'weight' => '29g'
                ],
            ],
            'Headset' => [
                [
                    'name' => 'SteelSeries Arctis Nova 7',
                    'description' => 'Wireless multi-platform gaming headset.',
                    'price' => 2800000.00,
                    'stock' => 10,
                    'image_url' => 'https://example.com/images/arctis7.jpg',
                    'connectivity' => 'Wireless (2.4GHz & Bluetooth)',
                    'weight' => '325g'
                ],
                [
                    'name' => 'Audeze Maxwell',
                    'description' => 'Planar Magnetic wireless gaming headset with 80hr battery.',
                    'price' => 5200000.00,
                    'stock' => 4,
                    'image_url' => 'https://example.com/images/maxwell.jpg',
                    'connectivity' => 'Wireless / Wired',
                    'weight' => '490g'
                ],
                [
                    'name' => 'HyperX Cloud III Wireless',
                    'description' => 'Legendary comfort and durability, now with 120-hour battery life.',
                    'price' => 2400000.00,
                    'stock' => 20,
                    'image_url' => 'https://example.com/images/cloud3.jpg',
                    'connectivity' => 'Wireless',
                    'weight' => '330g'
                ],
            ],
            'Mousepad' => [
                [
                    'name' => 'Artisan Ninja FX Zero Soft',
                    'description' => 'Premium high-performance gaming mousepad from Japan.',
                    'price' => 850000.00,
                    'stock' => 10,
                    'image_url' => 'https://example.com/images/artisan.jpg',
                    'weight' => '400g'
                ],
            ],
        ];

        foreach ($categories as $categoryName => $products) {
            $category = Category::firstOrCreate(['name' => $categoryName]);
            foreach ($products as $productData) {
                Product::updateOrCreate(
                    ['name' => $productData['name']],
                    array_merge($productData, ['category_id' => $category->id])
                );
            }
        }
    }
}
