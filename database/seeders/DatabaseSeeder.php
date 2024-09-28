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
            OfferSeeder::class,
            ProductSeeder::class,
            WarehouseSeeder::class,
            ExpressionSeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
