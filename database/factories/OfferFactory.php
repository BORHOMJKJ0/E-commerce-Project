<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+2 months');

        return [
            'discount_percentage' => fake()->randomFloat(2, 0, 99.99),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'product_id' => Product::all()->random()->id,
        ];
    }
}
