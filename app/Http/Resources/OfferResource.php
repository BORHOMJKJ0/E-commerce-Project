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
 *     @OA\Property(property="discount_percentage", type="number", format="float", example=15.50),
 *     @OA\Property(property="start_date", type="string", format="date", example="2024-09-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2024-12-31"),
 *     @OA\Property(property="product", type="object",
 *         @OA\Property(property="name", type="string", example="Smartphone"),
 *         @OA\Property(property="price", type="number", format="float", example=499.99),
 *         @OA\Property(property="description", type="string", example="Samsung A30s white color 8Ram 128GB space"),
 *         @OA\Property(property="category", type="string", example="Electronics"),
 *         @OA\Property(property="user", type="string", example="John Doe"),
 *     ),
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
