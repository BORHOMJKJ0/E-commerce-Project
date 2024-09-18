<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OfferResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Product A"),
 *     @OA\Property(property="image", type="string", example="https://example.com/product.jpg"),
 *     @OA\Property(property="price", type="number", format="float", example="99.99"),
 *     @OA\Property(property="description", type="string", example="it's a migical Product like from Random box"),
 *     @OA\Property(property="user", type="string", example="John Doe"),
 *     @OA\Property(property="total_amount", type="integer", example=500),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2025-12-31"),
 *     @OA\Property(property="category", type="string", example="Electronics"),
 * )
 */
class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'discount_percentage' => $this->discount_percentage,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'product' => $this->Product ? [
                'name' => $this->Product->name,
                'price' => $this->Product->price,
                'description' => $this->Product->description,
                'category' => $this->Product->Category ? $this->Product->Category->name : null,
                'user' => $this->Product->User ? $this->Product->User->name : null,
            ] : null,
        ];
    }
}
