<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CartResource",
 *     type="object",
 *     title="Cart Resource",
 *     description="Cart Resource schema",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the cart"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         description="The user who owns the cart",
 *         @OA\Property(property="id", type="integer", example=1, description="The ID of the user"),
 *         @OA\Property(property="name", type="string", example="John Doe", description="The full name of the cart's owner")
 *     ),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         description="List of products in the cart",
 *
 *         @OA\Items(
 *             type="object",
 *
 *             @OA\Property(property="id", type="integer", example=1, description="The ID of the product"),
 *             @OA\Property(property="name", type="string", example="Laptop", description="The name of the product"),
 *             @OA\Property(property="total_quantity", type="integer", example=5, description="The total quantity of this product in the cart"),
 *             @OA\Property(property="current_price", type="number", format="float", example=299.99, description="The current price of the product with the best offer applied"),
 *             @OA\Property(property="expiry_date", type="string", format="date",nullable=true,example="2024-12-31", description="The expiry date of the product (if applicable)"),
 *             @OA\Property(
 *                 property="main_image",
 *                 nullable=true,
 *                 type="object",
 *                 description="The main image of the product",
 *                 @OA\Property(property="id", type="integer", example=101, description="The ID of the image"),
 *                 @OA\Property(property="image", type="string", example="http://127.0.0.1:8000/storage/images/main_image_of_product.png", description="The URL of the image")
 *             ),
 *             @OA\Property(
 *                 property="best_offer",
 *                 type="object",
 *                 nullable=true,
 *                 description="The best available offer for the product",
 *                 @OA\Property(property="discount", type="string", example="15.00 %", description="The discount percentage"),
 *                 @OA\Property(property="start_date", type="string", format="date", example="2024-01-01", description="The start date of the offer"),
 *                 @OA\Property(property="end_date", type="string", format="date", example="2024-01-31", description="The end date of the offer")
 *             )
 *         )
 *     )
 * )
 */
class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $groupedItems = $this->cart_items->groupBy(function ($cartItem) {
            return $cartItem->warehouse->product->id;
        })->map(function ($items) {
            $firstItem = $items->first();
            $product = $firstItem->warehouse->product;

            $totalQuantity = $items->sum('quantity');

            $validWarehouses = $product->warehouses->filter(function ($warehouse) {
                return Carbon::parse($warehouse->expiry_date)->isFuture() || Carbon::parse($warehouse->expiry_date)->isToday();
            });
            $maxExpiryDate = $validWarehouses->max('expiry_date');

            $bestOffer = $validWarehouses->map(function ($warehouse) {
                return $warehouse->offers->filter(function ($offer) {
                    return Carbon::parse($offer->end_date)->isFuture() || Carbon::parse($offer->end_date)->isToday();
                });
            })->flatten()->sortByDesc('discount_percentage')->first();

            $currentPrice = $product->price;
            if ($bestOffer) {
                $discountPercentage = $bestOffer->discount_percentage;
                $discountedAmount = $product->price * ($discountPercentage / 100);
                $currentPrice = $product->price - $discountedAmount;
            }

            $mainImage = $product->images->where('main', 1)->first() ?? $product->images->first();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'total_quantity' => $totalQuantity,
                'current_price' => (float) $currentPrice,
                'expiry_date' => $maxExpiryDate ? Carbon::parse($maxExpiryDate)->format('Y-m-d') : null,
                'main_image' => $mainImage ? [
                    'id' => $mainImage->id,
                    'image' => $mainImage->image,
                ] : null,
                'best_offer' => $bestOffer ? [
                    'discount' => number_format($bestOffer->discount_percentage, 2).' %',
                    'start_date' => Carbon::parse($bestOffer->start_date)->format('Y-m-d'),
                    'end_date' => Carbon::parse($bestOffer->end_date)->format('Y-m-d'),
                ] : null,
            ];
        });

        return [
            'id' => $this->id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->first_name.' '.$this->user->last_name,
            ] : null,
            'items' => $groupedItems->values()->all(),
        ];
    }
}
