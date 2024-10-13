<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            Contact_typeSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            WarehouseSeeder::class,
            ExpressionSeeder::class,
            ReviewSeeder::class,
            CommentSeeder::class,
            OfferSeeder::class,
        ]);
    }
}
