<?php

namespace Database\Seeders;

use App\Models\Cart_items;
use Illuminate\Database\Seeder;

class CartItemsSeeder extends Seeder
{
    public function run(): void
    {
        Cart_items::factory(10)->create();
    }
}
