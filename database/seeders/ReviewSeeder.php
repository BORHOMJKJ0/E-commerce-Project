<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            Review::factory(5)->create()->each(function ($review) {
                $review->comment()->save(Comment::factory()->make());
            });
        });
    }
}
