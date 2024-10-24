<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="WarehouseResource",
 *     type="object",
 *     description="Warehouse resource containing product details and offers",
 *     @OA\Property(property="id", type="integer", description="Warehouse ID", example=1),
 *     @OA\Property(property="amount", type="integer", description="Amount of the product in the warehouse", example=86),
 *     @OA\Property(property="expiry_date", type="string", description="Expiry date of the product", format="date", example="2025-01-14"),
 *     @OA\Property(
 *         property="product",
 *         type="object",
 *         @OA\Property(property="name", type="string", example="incidunt in"),
 *         @OA\Property(property="price", type="number", format="float", example=3819.9989),
 *         @OA\Property(property="category", type="string", example="eos nulla"),
 *         @OA\Property(property="user", type="string", example="Broderick Bayer")
 *     ),
 *     @OA\Property(
 *         property="offers",
 *         type="array",
 *         description="List of offers for the product",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=3),
 *             @OA\Property(property="discount_percentage", type="number", format="float", example=28.41),
 *             @OA\Property(property="start_date", type="string", format="date", example="2024-10-19"),
 *             @OA\Property(property="end_date", type="string", format="date", example="2024-10-20"),
 *             @OA\Property(property="current_price", type="number", format="float", example=2734.74)
 *         )
 *     )
 * )
 */
class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $offersWithPrice = $this->offers->sortByDesc('discount_percentage')
            ->map(function ($offer) {
                $originalPrice = $this->product->price;
                $discountPercentage = $offer->discount_percentage;
                $discountAmount = $originalPrice * $discountPercentage / 100;
                $currentPrice = $originalPrice - $discountAmount;

                return [
                    'id' => $offer->id,
                    'discount_percentage' => (float) $offer->discount_percentage,
                    'start_date' => $offer->start_date->format('Y-m-d'),
                    'end_date' => $offer->end_date->format('Y-m-d'),
                    'current_price' => round($currentPrice, 2),
                ];
            })->values()
            ->toArray();

        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'expiry_date' => $this->expiry_date->format('Y-m-d'),
            'product' => [
                'name' => $this->product->name,
                'price' => $this->product->price,
                'category' => $this->product->category->name,
                'user' => $this->product->user->first_name.' '.$this->product->user->last_name,
            ],
            'offers' => $offersWithPrice,
        ];
    }
}
