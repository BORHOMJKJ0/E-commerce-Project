<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (range(1, 20) as $index) {
                Image::factory()->create();
            }
        });
    }
}
