<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'mobile' => $this->faker->unique()->phoneNumber,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'email_verified_at' => $this->faker->optional()->dateTime(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ];

    }
}
