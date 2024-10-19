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
 *     title="Product Resource",
 *     description="Product details",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the product"),
 *     @OA\Property(property="name", type="string", example="Iphone 15", description="The name of the product"),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         description="Images related to the product",
 *
 *         @OA\Items(
 *             type="object",
 *
 *             @OA\Property(property="image", type="string", description="Product Image URL"),
 *             @OA\Property(property="id", type="integer", example=1, description="Image ID"),
 *         )
 *     ),
 *     @OA\Property(property="price", type="number", format="float", example=499.99, description="The price of the product"),
 *     @OA\Property(property="description", type="string", example="A high-end smartphone with excellent features and a sleek design.", description="The description of the product"),
 *     @OA\Property(property="current_price", type="number", format="float", example=449.99, description="The current price of the product after discounts"),
 *     @OA\Property(property="user", type="string", example="Hasan Zaeter", description="The owner of the product"),
 *     @OA\Property(
 *         property="expressions",
 *         type="object",
 *         description="Expressions related to the product",
 *         @OA\Property(property="views", type="integer", example=120, description="The number of views of the product"),
 *         @OA\Property(property="likes", type="integer", example=45, description="The number of likes of the product"),
 *         @OA\Property(property="dislikes", type="integer", example=5, description="The number of dislikes of the product")
 *     ),
 *     @OA\Property(property="total_amount", type="integer", example=50, description="The total amount of product available"),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2025-12-31", description="The expiry date of the product"),
 *     @OA\Property(property="category", type="string", example="Smartphones", description="The category of the product"),
 *     @OA\Property(property="best_offer", type="object", description="Details of the best offer for the product",
 *         @OA\Property(property="discount", type="string", example="10.00 %", description="The discount percentage of the best offer"),
 *         @OA\Property(property="starting_at", type="string", format="date", example="2024-09-01", description="The start date of the best offer"),
 *         @OA\Property(property="ending_at", type="string", format="date", example="2024-12-31", description="The end date of the best offer")
 *     ),
 *     @OA\Property(property="max_expiry_date", type="string", format="date", example="2025-12-31", description="The maximum expiry date of all warehouses holding the product"),
 *     @OA\Property(property="comments", type="integer", example=5, description="The number of comments on the product"),
 *     @OA\Property(property="average_rating", type="number", format="float", example=4.5, description="The average rating of the product")
 * )
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $validWarehouses = $this->warehouses->filter(function ($warehouse) {
            return Carbon::parse($warehouse->expiry_date)->isFuture() || Carbon::parse($warehouse->expiry_date)->isToday();
        });

        $maxExpiryDate = $validWarehouses->max('expiry_date');

        $bestOffer = $validWarehouses->map(function ($warehouse) {
            return $warehouse->offers->filter(function ($offer) {

                return Carbon::parse($offer->end_date)->isFuture() || Carbon::parse($offer->end_date)->isToday();
            });
        })->flatten()->sortByDesc('discount_percentage')->first();

        $currentPrice = $this->price;
        if ($bestOffer) {
            $discountPercentage = $bestOffer->discount_percentage;
            $discountedAmount = $this->price * ($discountPercentage / 100);
            $currentPrice = $this->price - $discountedAmount;
        }
        $mainImage = $this->images->where('main', 1)->first() ?? $this->images->first();
        $ratingsCount = [
            '1_star' => $this->reviews->where('rating', 1)->count(),
            '2_star' => $this->reviews->where('rating', 2)->count(),
            '3_star' => $this->reviews->where('rating', 3)->count(),
            '4_star' => $this->reviews->where('rating', 4)->count(),
            '5_star' => $this->reviews->where('rating', 5)->count(),
        ];

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $mainImage ? [
                'id' => $mainImage->id,
                'image' => $mainImage->image,
            ] : null,
            'price' => (float) $this->price,
            'current_price' => (float) $currentPrice,
            'user' => $this->user->first_name.' '.$this->user->last_name,

            'best_offer' => $bestOffer ? [
                'discount' => number_format($bestOffer->discount_percentage, 2, '.', '').' %',
                'starting_at' => Carbon::parse($bestOffer->start_date)->format('Y-n-j'),
                'ending_at' => Carbon::parse($bestOffer->end_date)->format('Y-n-j'),
            ] : null,

            'max_expiry_date' => $maxExpiryDate ? Carbon::parse($maxExpiryDate)->format('Y-n-j') : null,

            'total_amount' => (float) $this->warehouses->sum('amount'),
            'category' => $this->category->name,
            'comments' => $this->comments->count(),
            'average_rating' => $this->reviews->avg('rating') ?: 0,
        ];

        if ($request->routeIs('products.show')) {
            unset($data['image']);
            $data['ratings_count'] = $ratingsCount;
            $data['images'] = $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => $image->image,
                ];
            });
            $data['description'] = $this->description;
            $data['reviewers'] = $this->reviews ? $this->reviews->map(function ($review) {
                return [
                    'name' => $review->user->first_name.' '.$review->user->last_name,
                    'rating' => $review->rating,
                    'comment' => $review->comment ? [
                        'id' => $review->comment->id,
                        'text' => $review->comment->text ?? null,
                        'image' => $review->comment->image ?? null,
                    ] : null,
                    'created_at' => $review->created_at->format('Y-m-d'),
                ];
            }) : [];
        }

        return $data;
    }
}
