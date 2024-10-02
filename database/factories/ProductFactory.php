<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'image' => fake()->imageUrl(200, 200),
            'description' => fake()->realText(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'category_id' => Category::all()->random()->id,
            'user_id' => User::all()->random()->id,
        ];

    }
}
