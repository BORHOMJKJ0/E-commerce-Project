<?php

namespace Database\Seeders;

use App\Models\Contact_information;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Contact_informationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            Contact_information::factory(10)->create();
        });
    }
}
