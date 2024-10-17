<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

class ImageResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="ImageResource",
     *     type="object",
     *     title="Image Resource",
     *
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="image", type="string", example="https://example.com/image.jpg"),
     *     @OA\Property(property="product", type="object", description="Product related to the image",
     *     @OA\Property(property="id", type="integer", example=1, description="The ID of the product"),
     *     @OA\Property(property="name", type="string", example="Iphone 15", description="The name of the product"),
     *     @OA\Property(property="price", type="number", format="float", example=499.99, description="The price of the product"),
     *     @OA\Property(property="description", type="string", example="A high-end smartphone with excellent features and a sleek design.", description="The description of the product"),
     *     @OA\Property(property="category", type="string", example="Smartphones", description="The category of the product"),
     *     @OA\Property(property="user", type="string", example="Hasan Zaeter", description="The owner of the product"),
     *     )
     * )
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => $this->image,
            'product' => $this->Product ? [
                'id' => $this->Product->id,
                'name' => $this->Product->name,
                'price' => (float) $this->Product->price,
                'description' => $this->Product->description,
                'category' => $this->Product->Category ? $this->Product->Category->name : null,
                'user' => $this->Product->User->first_name.' '.$this->Product->User->last_name,
            ] : null,
        ];
    }
}
