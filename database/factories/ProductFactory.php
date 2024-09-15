<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'image' => fake()->imageUrl(200, 200),
            'price' => fake()->randomFloat(2, 10, 1000),
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
        ];

    }
}
