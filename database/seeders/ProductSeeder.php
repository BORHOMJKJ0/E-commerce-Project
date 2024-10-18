<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            Product::factory()
                ->for(User::factory(), 'user')
                ->for(Category::factory(), 'category')
                ->create();
        });
    }
}
