<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OfferResource",
 *     type="object",
 *     title="Offer Resource",
 *     description="Offer details including warehouse and product information",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the offer"),
 *     @OA\Property(property="discount_percentage", type="string", example="10.00 %", description="The discount percentage of the offer"),
 *     @OA\Property(property="start_date", type="string", format="date", example="2024-09-01", description="The start date of the offer"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2024-12-31", description="The end date of the offer"),
 *     @OA\Property(property="warehouse", type="object", description="Warehouse related to the offer",
 *         @OA\Property(property="amount", type="number", format="float", example=100, description="The amount of the product in the warehouse"),
 *         @OA\Property(property="expiry_date", type="string", format="date", example="2024-10-31", description="The expiry date of the warehouse stock")
 *     ),
 *     @OA\Property(property="product", type="object", description="Product related to the offer",
 *         @OA\Property(property="id", type="integer", example=11, description="The ID of the product"),
 *         @OA\Property(property="name", type="string", example="Banana", description="The name of the product"),
 *         @OA\Property(property="price", type="number", format="float", example=22021320, description="The price of the product")
 *     )
 * )
 */
class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $warehouse = $this->warehouse;
        $product = $warehouse ? $warehouse->product : null;

        return [
            'id' => $this->id,
            'discount_percentage' => number_format($this->discount_percentage, 2, '.', '').' %',
            'start_date' => $this->start_date ? $this->start_date->format('Y-n-j') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-n-j') : null,

            'warehouse' => $warehouse ? [
                'amount' => (float) $warehouse->amount,
                'expiry_date' => $warehouse->expiry_date ? $warehouse->expiry_date->format('Y-n-j') : null,
            ] : null,

            'product' => $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
            ] : null,
        ];
    }
}
