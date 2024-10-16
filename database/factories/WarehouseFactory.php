<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount' => fake()->numberBetween(1, 100),
            'expiry_date' => fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
            'product_id' => Product::all()->random()->id,
        ];
    }
}
