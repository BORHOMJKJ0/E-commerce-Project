<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    public function definition(): array
    {
        $paymentDate = fake()->dateTimeBetween('-1 month', 'now');
        $expiryDate = fake()->dateTimeBetween($paymentDate, '+2 months');
        $amount = fake()->randomNumber(2);

        return [
            'pure_price' => fake()->randomFloat(2, 100, 1000),
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'settlement_date' => $amount > 0 ? null : fake()->dateTimeBetween($paymentDate, $expiryDate),
            'expiry_date' => $expiryDate,
            'product_id' => Product::factory(),
        ];
    }
}
