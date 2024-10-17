<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the warehouse"),
 *     @OA\Property(property="amount", type="integer", example=250, description="The amount of the product in the warehouse"),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2025-12-31", description="The expiry date of the warehouse"),
 *     @OA\Property(
 *         property="product",
 *         type="object",
 *         description="Product details related to the warehouse",
 *         @OA\Property(property="name", type="string", example="Iphone 15", description="The name of the product"),
 *         @OA\Property(property="price", type="number", format="float", example=499.99, description="The price of the product"),
 *         @OA\Property(property="category", type="string", example="Smartphone", description="The category of the product"),
 *         @OA\Property(property="user", type="string", example="Hasan Zaeter", description="The owner of the product"),
 *         @OA\Property(property="current_price", type="string", example="449.99", description="The current price of the product after discounts"),
 *     ),
 *     @OA\Property(
 *         property="offers",
 *         type="array",
 *         description="List of current offers for the product in the warehouse",
 *
 *         @OA\Items(
 *             type="object",
 *
 *             @OA\Property(property="id", type="integer", example=1, description="The ID of the offer"),
 *             @OA\Property(property="discount_percentage", type="string", example="10.00 %", description="The discount percentage of the offer"),
 *             @OA\Property(property="start_date", type="string", format="date", example="2024-09-01", description="The start date of the offer"),
 *             @OA\Property(property="end_date", type="string", format="date", example="2024-12-31", description="The end date of the offer")
 *         )
 *     )
 * )
 */
class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $productPrice = $this->Product ? (float) $this->Product->price : 0;

        $filteredOffers = $this->offers->filter(function ($offer) {
            return Carbon::parse($offer->end_date)->isFuture() || Carbon::parse($offer->end_date)->isToday();
        })->sortByDesc('discount_percentage');

        $highestDiscountOffer = $filteredOffers->first();
        $discountPercentage = $highestDiscountOffer ? $highestDiscountOffer->discount_percentage : 0;

        $discountedAmount = $productPrice * ($discountPercentage / 100);
        $currentPrice = $productPrice - $discountedAmount;

        return [
            'id' => $this->id,
            'amount' => (int) $this->amount,
            'expiry_date' => $this->expiry_date ? $this->expiry_date->format('Y-n-j') : null,
            'product' => $this->Product ? [
                'name' => $this->Product->name,
                'price' => $productPrice,
                'category' => $this->Product->Category ? $this->Product->Category->name : null,
                'user' => $this->Product->User->first_name.' '.$this->Product->User->last_name,
                'current_price' => number_format($currentPrice, 2, '.', ''),
            ] : null,

            'offers' => $filteredOffers->map(function ($offer) {
                return [
                    'id' => $offer->id,
                    'discount_percentage' => number_format($offer->discount_percentage, 2, '.', '').' %',
                    'start_date' => $offer->start_date ? $offer->start_date->format('Y-n-j') : null,
                    'end_date' => $offer->end_date ? $offer->end_date->format('Y-n-j') : null,
                ];
            }),
        ];
    }
}
