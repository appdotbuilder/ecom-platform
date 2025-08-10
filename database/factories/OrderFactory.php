<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(100000, 10000000);
        $shippingCost = $this->faker->numberBetween(10000, 100000);
        $taxAmount = $subtotal * 0.1; // 10% tax
        $discountAmount = $this->faker->numberBetween(0, $subtotal * 0.1);
        
        return [
            'order_number' => Order::generateOrderNumber(),
            'user_id' => User::factory(),
            'reseller_id' => null,
            'order_type' => $this->faker->randomElement(['online', 'pos']),
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $subtotal + $shippingCost + $taxAmount - $discountAmount,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded', 'hutang']),
            'payment_method' => $this->faker->randomElement(['midtrans', 'xendit', 'bank_transfer', 'hutang', 'cash']),
            'shipping_address' => [
                'name' => $this->faker->name(),
                'address' => $this->faker->address(),
                'city' => $this->faker->city(),
                'postal_code' => $this->faker->postcode(),
                'phone' => $this->faker->phoneNumber(),
            ],
            'billing_address' => [
                'name' => $this->faker->name(),
                'address' => $this->faker->address(),
                'city' => $this->faker->city(),
                'postal_code' => $this->faker->postcode(),
            ],
            'shipping_service' => $this->faker->randomElement(['JNE', 'TIKI', 'POS Indonesia']),
            'shipping_service_type' => $this->faker->randomElement(['REG', 'YES', 'OKE']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'payment_status' => 'paid',
            'delivered_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}