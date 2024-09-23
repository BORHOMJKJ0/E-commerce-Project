<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="WarehouseResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="pure_price", type="number", format="float", example="350.50"),
 *     @OA\Property(property="amount", type="number", format="integer", example="250"),
 *     @OA\Property(property="payment_date", type="string", format="date", example="2024-09-15"),
 *     @OA\Property(property="settlement_date", type="string", format="date", example="null"),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2025-12-31"),
 *     @OA\Property(
 *         property="product",
 *         type="object",
 *         @OA\Property(property="name", type="string", example="Iphone 15"),
 *         @OA\Property(property="price", type="number", format="float", example="499.99"),
 *         @OA\Property(property="category", type="string", example="Smartphone"),
 *         @OA\Property(property="user", type="string", example="Hasan Zaeter")
 *     ),
 * )
 */
class WarehouseResource extends JsonResource
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
            'pure_price' => $this->pure_price,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'settlement_date' => $this->settlement_date,
            'expiry_date' => $this->expiry_date,
            'product' => $this->Product ? [
                'name' => $this->Product->name,
                'price' => $this->Product->price,
                'category' => $this->Product->Category ? $this->Product->Category->name : null,
                'user' => $this->Product->User ? $this->Product->User->name : null,
            ] : null,
        ];
    }
}
