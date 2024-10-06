<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        Review::factory(5)->create()->each(function ($review) {
            $review->comment()->save(Comment::factory()->make());
        });
    }
}
