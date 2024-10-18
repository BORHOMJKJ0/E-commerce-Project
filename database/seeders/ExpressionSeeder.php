<?php

namespace Database\Seeders;

use App\Models\Expression;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpressionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            Expression::factory(10)->create();
        });
    }
}
