<?php

namespace Database\Factories;

use App\Models\Contact_type;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class Contact_informationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'link' => $this->faker->url,
            'user_id' => User::all()->random()->id,
            'contact_type_id' => Contact_type::all()->random()->id,
        ];
    }
}
