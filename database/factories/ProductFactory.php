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
            'price' => fake()->randomFloat(),
            'description' => fake()->realText(),
            'category_id' => Category::all()->random()->id,
            'user_id' => User::all()->random()->id,
        ];

    }
}
