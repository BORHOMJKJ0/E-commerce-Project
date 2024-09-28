<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class Contact_typeFactory extends Factory
{
    public function definition(): array
    {
        $contactTypes = [
            ['type_arabic' => 'رقم الهاتف الخاص بالعمل', 'type_english' => 'Business_phoneNumber'],
            ['type_arabic' => 'رقم العمل', 'type_english' => 'Business_number'],
            ['type_arabic' => 'البريد الإلكتروني الخاص بالعمل', 'type_english' => 'Business_email'],
            ['type_arabic' => 'حساب فيسبوك', 'type_english' => 'facebook_account'],
            ['type_arabic' => 'حساب انستغرام', 'type_english' => 'instagram_account'],
            ['type_arabic' => 'قناة يوتيوب', 'type_english' => 'youtube_channel'],
            ['type_arabic' => 'حساب سناب شات', 'type_english' => 'snapchat_account'],
            ['type_arabic' => 'حساب تويتر', 'type_english' => 'twitter_account'],
            ['type_arabic' => 'حساب لينكد ان', 'type_english' => 'linkedin_account'],
            ['type_arabic' => 'حساب تيك توك', 'type_english' => 'tiktok_account'],
        ];

        static $index = 0;

        $data = $contactTypes[$index];

        $index++;

        return $data;
    }
}
