<?php

namespace Database\Seeders;

use App\Models\contact_type;
use Illuminate\Database\Seeder;

class Contact_typeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        contact_type::create([
            'id' => '1',
            'type_arabic' => 'حساب فيسبوك ',
            'type_english' => 'facebook_account',
        ]);
        contact_type::create([
            'id' => '2',
            'type_arabic' => 'حساب انستغرام ',
            'type_english' => 'instagram_account',
        ]);
        contact_type::create([
            'id' => '3',
            'type_arabic' => 'حساب لينكد ان ',
            'type_english' => 'linkedin_account',
        ]);
        contact_type::create([
            'id' => '4',
            'type_arabic' => 'حساب تويتر ',
            'type_english' => 'twitter_account',
        ]);
//        contact_type::create([
//            'id' => '5',
//            'type-arabic' => 'حساب فيسبوك ',
//            'type-english' => 'facebook_account',
//        ]);
    }
}
