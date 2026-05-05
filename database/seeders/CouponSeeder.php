<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coupon::updateOrCreate(
            ['code' => 'GAMER20'],
            [
                'discount_percentage' => 20.00,
                'valid_until' => now()->addDays(30),
                'usage_limit' => 100,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => '50KOFF'],
            [
                'discount_amount' => 50000.00,
                'valid_until' => now()->addDays(30),
                'usage_limit' => 50,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'EXPIRED'],
            [
                'discount_percentage' => 10.00,
                'valid_until' => now()->subDays(1),
                'usage_limit' => 10,
            ]
        );
    }
}
