<?php

namespace Database\Seeders;

use App\Models\offer;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        offer::factory(5)->create();
    }
}
