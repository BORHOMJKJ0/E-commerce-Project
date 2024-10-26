<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class Cart_itemsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quantity' => fake()->numberBetween(1, 100),
            'product_id' => Product::all()->random()->id,
            'cart_id' => Cart::all()->random()->id,
        ];
    }
}
