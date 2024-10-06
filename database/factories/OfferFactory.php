<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::with('warehouses')
            ->whereHas('warehouses', function ($query) {
                $query->where('amount', '>', 0)
                    ->where('expiry_date', '>', now());
            })
            ->inRandomOrder()
            ->first();
        if (! $product) {
            $product = Product::factory()->create();
        }
        $startDate = fake()->dateTimeBetween('-1 month', 'now');
        $endDate = fake()->dateTimeBetween($startDate, $product->warehouses->min('expiry_date'));

        return [
            'discount_percentage' => fake()->randomFloat(2, 0, 99.99),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'product_id' => $product->id,
        ];
    }
}
