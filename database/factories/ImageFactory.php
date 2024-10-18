<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'image' => fake()->imageUrl(200, 200),
            'product_id' => Product::all()->random()->id,
        ];
    }
}
