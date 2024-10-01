<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->name,
            'email' => fake()->unique()->safeEmail,
            'mobile' => fake()->unique()->phoneNumber,
            'gender' => fake()->randomElement(['male', 'female']),
            'email_verified_at' => fake()->optional()->dateTime(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ];

    }
}
