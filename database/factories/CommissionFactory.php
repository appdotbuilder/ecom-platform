<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commission>
 */
class CommissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderAmount = $this->faker->numberBetween(100000, 10000000);
        $commissionPercentage = $this->faker->numberBetween(1, 12);
        $commissionAmount = ($orderAmount * $commissionPercentage) / 100;
        
        return [
            'order_id' => Order::factory(),
            'reseller_id' => User::factory(),
            'from_user_id' => User::factory(),
            'level' => $this->faker->numberBetween(1, 10),
            'order_amount' => $orderAmount,
            'commission_percentage' => $commissionPercentage,
            'commission_amount' => $commissionAmount,
            'status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
            'type' => $this->faker->randomElement(['reseller', 'affiliate']),
        ];
    }

    /**
     * Indicate that the commission is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the commission is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}