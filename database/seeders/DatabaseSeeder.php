<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ResellerLevelSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        // Create test admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'user_type' => 'admin',
        ]);

        // Create test reseller with Bronze level
        User::factory()->create([
            'name' => 'Test Reseller',
            'email' => 'reseller@example.com',
            'user_type' => 'reseller',
            'reseller_level_id' => 1, // Bronze level
            'is_affiliate' => true,
            'affiliate_code' => 'AFF12345678',
        ]);

        // Create test customer
        User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'user_type' => 'customer',
        ]);
    }
}
