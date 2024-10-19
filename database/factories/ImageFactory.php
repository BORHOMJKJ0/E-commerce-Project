<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    public function definition(): array
    {
        $product_id = Product::all()->random()->id;

        $hasMain = Image::where('product_id', $product_id)
            ->where('main', 1)
            ->exists();

        return [
            'image' => $this->faker->imageUrl(200, 200),
            'main' => $hasMain ? 0 : 1,
            'product_id' => $product_id,
        ];
    }
}
