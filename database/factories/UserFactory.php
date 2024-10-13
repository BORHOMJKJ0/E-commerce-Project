<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'First_Name' => fake()->firstName,
            'Last_Name' => fake()->optional()->lastName,
            'email' => fake()->unique()->safeEmail,
            'mobile' => fake()->unique()->phoneNumber,
            'email_verified_at' => fake()->optional()->dateTime(),
            'password' => bcrypt('password'),
            'Address' => fake()->optional()->address,
            'remember_token' => Str::random(10),
        ];
    }
}
