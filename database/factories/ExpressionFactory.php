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
            'action' => $this->faker->randomElement(['like', 'dislike']),
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
        ];
    }
}
