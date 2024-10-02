<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'text' => fake()->sentence,
            'image' => fake()->optional()->imageUrl(200, 200),
            'review_id' => Review::all()->random()->id,
        ];
    }
}
