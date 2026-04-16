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
            ],
        ];

        foreach ($categories as $categoryName => $products) {
            $category = Category::firstOrCreate(['name' => $categoryName]);
            foreach ($products as $productData) {
                $category->products()->create($productData);
            }
        }
    }
}
