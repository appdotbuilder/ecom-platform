<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PosSession>
 */
class PosSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $openingCash = $this->faker->numberBetween(500000, 2000000);
        $totalSales = $this->faker->numberBetween(0, 5000000);
        $closingCash = $openingCash + $totalSales;
        
        return [
            'cashier_id' => User::factory(),
            'opening_cash' => $openingCash,
            'closing_cash' => $closingCash,
            'total_sales' => $totalSales,
            'total_transactions' => $this->faker->numberBetween(0, 50),
            'opened_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'closed_at' => $this->faker->dateTimeBetween('now', '+1 day'),
            'status' => $this->faker->randomElement(['open', 'closed']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the session is open.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
            'closed_at' => null,
            'closing_cash' => null,
        ]);
    }

    /**
     * Indicate that the session is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
            'closed_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
        ]);
    }
}