<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->call([
                Contact_typeSeeder::class,
                UserSeeder::class,
                CategorySeeder::class,
                ProductSeeder::class,
                ImageSeeder::class,
                WarehouseSeeder::class,
                ExpressionSeeder::class,
                ReviewSeeder::class,
                CommentSeeder::class,
                OfferSeeder::class,
            ]);
        });
    }
}
