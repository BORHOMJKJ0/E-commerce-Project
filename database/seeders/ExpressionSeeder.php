<?php

namespace Database\Seeders;

use App\Models\Expression;
use Illuminate\Database\Seeder;

class ExpressionSeeder extends Seeder
{
    public function run(): void
    {
        Expression::factory(10)->create();
    }
}
