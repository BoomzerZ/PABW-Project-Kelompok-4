<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pabw.com'],
            [
                'name' => 'Admin Marketplace',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );
    }
}
