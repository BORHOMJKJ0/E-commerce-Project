<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
