<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CartItemsResource",
 *     type="object",
 *     title="Cart Item Resource",
 *     description="Cart Item Resource schema",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the cart item"),
 *     @OA\Property(property="quantity", type="integer", example=2, description="The quantity of the product in the cart item"),
 *     @OA\Property(
 *         property="cart",
 *         type="object",
 *         description="The cart to which this item belongs",
 *         @OA\Property(property="id", type="integer", example=1, description="The ID of the cart"),
 *         @OA\Property(property="user_name", type="string", example="John Doe", description="The full name of the cart's owner")
 *     ),
 *     @OA\Property(
 *         property="product",
 *         type="object",
 *         description="The product details",
 *         @OA\Property(property="id", type="integer", example=1, description="The ID of the product"),
 *         @OA\Property(property="name", type="string", example="Laptop", description="The name of the product"),
 *         @OA\Property(property="category", type="string", example="Electronics", description="The category of the product"),
 *         @OA\Property(
 *             property="user",
 *             type="object",
 *             description="The user who owns the product",
 *             @OA\Property(property="id", type="integer", example=10, description="The ID of the product's owner"),
 *             @OA\Property(property="name", type="string", example="John Doe", description="The full name of the product's owner")
 *         )
 *     )
 * )
 */
class CartItemsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'cart' => $this->cart ? [
                'id' => $this->cart->id,
                'user_name' => optional($this->cart->user)->first_name.' '.optional($this->cart->user)->last_name,
            ] : null,
            'product' => $this->warehouse && $this->warehouse->product ? [
                'id' => $this->warehouse->product->id,
                'name' => $this->warehouse->product->name,
                'category' => optional($this->warehouse->product->category)->name,
                'user' => [
                    'id' => optional($this->warehouse->product->user)->id,
                    'name' => optional($this->warehouse->product->user)->first_name.' '.optional($this->warehouse->product->user)->last_name,
                ],
            ] : null,
        ];
    }
}
