<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {

        return [
            'title' => fake()->sentence,
            'text' => fake()->realText,
            'review_id' => Review::all()->random()->id,
        ];
    }
}
