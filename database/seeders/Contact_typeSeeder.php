<?php

namespace Database\Seeders;

use App\Models\Contact_type;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Contact_typeSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            Contact_type::factory(10)->create();
        });
    }
}
