<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'rating' => fake()->randomFloat(2, 1, 5),
        ];
    }
}
