<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="WarehouseResource",
 *     type="object",
 *     title="Warehouse Resource",
 *     description="Warehouse details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the warehouse"),
 *     @OA\Property(property="pure_price", type="number", format="float", example="350.50",description="The pure price of the warehouse"),
 *     @OA\Property(property="amount", type="number", format="integer", example="250",description="The amount of the warehouse"),
 *     @OA\Property(property="payment_date", type="string", format="date", example="2024-09-15",description="The payment date of the warehouse"),
 *     @OA\Property(property="settlement_date", type="string", format="date", example="null",description="The settlement date of the warehouse"),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2025-12-31",description="The expiry date of the warehouse"),
 *     @OA\Property(
 *         property="product",
 *         type="object",
 *        description="Product Detalis related to the warehouse",
 *         @OA\Property(property="name", type="string", example="Iphone 15",description="The name of the product"),
 *         @OA\Property(property="price", type="number", format="float", example="499.99",description="The price of the product"),
 *         @OA\Property(property="category", type="string", example="Smartphone",description="The category of the product"),
 *         @OA\Property(property="user", type="string", example="Hasan Zaeter",description="The owner of the product")
 *     ),
 * )
 */
class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pure_price' => (float) $this->pure_price,
            'amount' => (int) $this->amount,
            'payment_date' => $this->payment_date ? $this->payment_date->format('Y-n-j') : null,
            'settlement_date' => $this->settlement_date ? $this->settlement_date->format('Y-n-j') : null,
            'expiry_date' => $this->expiry_date ? $this->expiry_date->format('Y-n-j') : null,
            'product' => $this->Product ? [
                'name' => $this->Product->name,
                'price' => (float) $this->Product->price,
                'category' => $this->Product->Category ? $this->Product->Category->name : null,
                'user' => $this->Product->User ? $this->Product->User->name : null,
            ] : null,
        ];
    }
}
