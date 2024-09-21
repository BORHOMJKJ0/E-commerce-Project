<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Product A"),
 *     @OA\Property(property="image", type="string", example="https://example.com/product.jpg"),
 *     @OA\Property(property="price", type="number", format="float", example=99.99),
 *     @OA\Property(property="description", type="string", example="It's a magical product from Random Box"),
 *     @OA\Property(property="current_price", type="number", format="float", example=84.49),
 *     @OA\Property(property="user", type="string", example="John Doe"),
 *     @OA\Property(
 *         property="offers",
 *         type="array",
 *
 *         @OA\Items(
 *
 *             @OA\Property(property="discount", type="string", example="15.50"),
 *             @OA\Property(property="starting_at", type="string", format="date-time", example="2024-09-21 23:20"),
 *             @OA\Property(property="ending_at", type="string", format="date-time", example="2024-09-21 23:20")
 *         )
 *     ),
 *     @OA\Property(property="total_amount", type="integer", example=100),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2024-12-01"),
 *     @OA\Property(property="category", type="string", example="Electronics"),
 * )
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $currentDate = Carbon::now();

        $currentPrice = $this->price;
        $activeOffer = null;
        if ($this->offers) {
            foreach ($this->offers as $offer) {
                if ($currentDate >= $offer->start_date && $currentDate <= $offer->end_date) {
                    $activeOffer = $offer;
                    break;
                }
            }
        }

        if ($activeOffer) {
            $discountPercentage = $activeOffer->discount_percentage;
            $discountedAmount = $this->price * ($discountPercentage / 100);
            $currentPrice = $this->price - $discountedAmount;
        }
        $activeWarehouses = $this->warehouses->where('expiry_date', '>=', Carbon::now())
            ->where('amount', '>', 0);
        $minExpiryDate = $activeWarehouses->min('expiry_date');

        return [

            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'price' => $this->price,
            'description' => $this->description,
            'current_price' => $currentPrice,
            'user' => $this->user->name,
            'offers' => $this->offers->map(function ($offer) {
                return [
                    'discount' => $offer->discount_percentage,
                    'starting_at' => Carbon::parse($offer->offer_start)->format('Y-m-d H:i'),
                    'ending_at' => Carbon::parse($offer->offer_end)->format('Y-m-d H:i'),
                ];
            }),
            'total_amount' => $this->warehouses->sum('amount'),
            'expiry_date' => $minExpiryDate ?: null,
            'category' => $this->category->name,
        ];
    }
}
