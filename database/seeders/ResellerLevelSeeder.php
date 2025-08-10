<?php

namespace Database\Seeders;

use App\Models\ResellerLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResellerLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'name' => 'Bronze',
                'level' => 1,
                'discount_percentage' => 5.00,
                'commission_percentage' => 2.00,
                'min_sales_amount' => 0,
                'description' => 'Entry level reseller with basic benefits',
            ],
            [
                'name' => 'Silver',
                'level' => 2,
                'discount_percentage' => 8.00,
                'commission_percentage' => 3.00,
                'min_sales_amount' => 1000000,
                'description' => 'Silver level with better discounts and commissions',
            ],
            [
                'name' => 'Gold',
                'level' => 3,
                'discount_percentage' => 12.00,
                'commission_percentage' => 4.00,
                'min_sales_amount' => 2500000,
                'description' => 'Gold level with enhanced benefits',
            ],
            [
                'name' => 'Platinum',
                'level' => 4,
                'discount_percentage' => 15.00,
                'commission_percentage' => 5.00,
                'min_sales_amount' => 5000000,
                'description' => 'Platinum level with premium benefits',
            ],
            [
                'name' => 'Diamond',
                'level' => 5,
                'discount_percentage' => 18.00,
                'commission_percentage' => 6.00,
                'min_sales_amount' => 10000000,
                'description' => 'Diamond level with exclusive benefits',
            ],
            [
                'name' => 'Master',
                'level' => 6,
                'discount_percentage' => 22.00,
                'commission_percentage' => 7.00,
                'min_sales_amount' => 20000000,
                'description' => 'Master level for top performers',
            ],
            [
                'name' => 'Grand Master',
                'level' => 7,
                'discount_percentage' => 25.00,
                'commission_percentage' => 8.00,
                'min_sales_amount' => 35000000,
                'description' => 'Grand Master level with maximum benefits',
            ],
            [
                'name' => 'Elite',
                'level' => 8,
                'discount_percentage' => 28.00,
                'commission_percentage' => 9.00,
                'min_sales_amount' => 50000000,
                'description' => 'Elite level for exceptional performers',
            ],
            [
                'name' => 'Supreme',
                'level' => 9,
                'discount_percentage' => 32.00,
                'commission_percentage' => 10.00,
                'min_sales_amount' => 75000000,
                'description' => 'Supreme level with ultimate benefits',
            ],
            [
                'name' => 'Legendary',
                'level' => 10,
                'discount_percentage' => 35.00,
                'commission_percentage' => 12.00,
                'min_sales_amount' => 100000000,
                'description' => 'Legendary level - the highest achievement',
            ],
        ];

        foreach ($levels as $level) {
            ResellerLevel::create($level);
        }
    }
}