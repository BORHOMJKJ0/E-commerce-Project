<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        $review = Review::doesntHave('comment')->inRandomOrder()->first();
        if (! $review) {
            $review = Review::factory()->create();
        }
        $hasText = rand(0, 1) == 1;
        $hasImage = rand(0, 1) == 1;

        if (! $hasText && ! $hasImage) {
            $hasText = true;
        }

        return [
            'text' => $hasText ? fake()->sentence : null,
            'image' => $hasImage ? fake()->imageUrl(200, 200) : null,
            'review_id' => $review->id,
        ];
    }
}
