<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResellerLevel>
 */
class ResellerLevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond']),
            'level' => $this->faker->numberBetween(1, 10),
            'discount_percentage' => $this->faker->numberBetween(5, 35),
            'commission_percentage' => $this->faker->numberBetween(2, 12),
            'min_sales_amount' => $this->faker->numberBetween(0, 100000000),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the reseller level is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the reseller level is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}