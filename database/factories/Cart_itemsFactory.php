<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class Cart_itemsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quantity' => fake()->numberBetween(1, 100),
            'warehouse_id' => Warehouse::all()->random()->id,
            'cart_id' => Cart::all()->random()->id,
        ];
    }
}
