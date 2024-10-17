<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            Offer::factory(10)->create();
        });
    }
}
