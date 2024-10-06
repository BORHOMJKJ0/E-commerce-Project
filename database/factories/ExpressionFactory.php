<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpressionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'action' => fake()->randomElement(['like', 'dislike']),
            'user_id' => User::all()->random()->id,
            'product_id' => Product::all()->random()->id,
        ];
    }
}
