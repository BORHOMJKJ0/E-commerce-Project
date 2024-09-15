<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentDate = fake()->dateTimeBetween('-1 month', 'now');
        $expiryDate = fake()->dateTimeBetween($paymentDate, '+2 months');
        $settlementDate = fake()->dateTimeBetween($paymentDate, $expiryDate);

        return [
            'pure_price' => fake()->randomFloat(2, 100, 1000),
            'amount' => fake()->randomNumber(2),
            'payment_date' => $paymentDate,
            'settlement_date' => $settlementDate,
            'expiry_date' => $expiryDate,
            'product_id' => Product::factory(),
        ];
    }
}
