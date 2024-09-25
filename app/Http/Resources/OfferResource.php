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
 *     @OA\Property(property="discount_percentage", type="number", format="float", example=10.00),
 *     @OA\Property(property="start_date", type="string", format="date", example="2024-09-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2024-12-31"),
 *     @OA\Property(property="product", type="object",
 *         @OA\Property(property="name", type="string", example="Iphone 15"),
 *         @OA\Property(property="price", type="number", format="float", example=499.99),
 *         @OA\Property(property="description", type="string", example="A high-end smartphone with excellent features and a sleek design"),
 *         @OA\Property(property="category", type="string", example="Smartphone"),
 *         @OA\Property(property="user", type="string", example="Hasan Zaeter"),
 *     ),
 * )
 */
class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'discount_percentage' => number_format($this->discount_percentage, 2, '.', '').' %',
            'start_date' => $this->start_date ? $this->start_date->format('Y-n-j') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-n-j') : null,
            'product' => $this->Product ? [
                'name' => $this->Product->name,
                'price' => (float) $this->Product->price,
                'description' => $this->Product->description,
                'category' => $this->Product->Category ? $this->Product->Category->name : null,
                'user' => $this->Product->User ? $this->Product->User->name : null,
            ] : null,
        ];
    }
}
