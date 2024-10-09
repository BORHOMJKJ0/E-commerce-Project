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
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the product"),
 *     @OA\Property(property="name", type="string", example="Iphone 15",description="The name of the product"),
 *     @OA\Property(property="image", type="string", example="https://example.com/images/smartphone-xyz.jpg",description="The image of the product"),
 *     @OA\Property(property="price", type="number", format="float", example=499.99,description="The price of the product"),
 *     @OA\Property(property="description", type="string", example="A high-end smartphone with excellent features and a sleek design.",description="The description of the product"),
 *     @OA\Property(property="current_price", type="number", format="float", example=449.99,description="The current price of the product"),
 *     @OA\Property(property="user", type="string", example="Hasan Zaeter",description="The owner of the product"),
 *     @OA\Property(
 *         property="offers",
 *         type="array",
 *         description="Offer related to the product",
 *
 *         @OA\Items(
 *
 *             @OA\Property(property="discount", type="string", example="10.00",description="The discount percentage of the offer"),
 *             @OA\Property(property="starting_at", type="string", format="date-time", example="2024-09-22 12:00",description="The start date of the offer"),
 *             @OA\Property(property="ending_at", type="string", format="date-time", example="2025-01-15 23:59",description="The end date of the offer")
 *         )
 *     ),
 *     @OA\Property(
 *          property="expressions",
 *          type="object",
 *          description="Expressions related to the product",
 *          @OA\Property(property="views", type="integer", example=120,description="The Number of views of the product"),
 *          @OA\Property(property="likes", type="integer", example=45,description="The Number of likes of the product"),
 *          @OA\Property(property="dislikes", type="integer", example=5,description="The Number of dislikes of the product")
 *      ),
 *     @OA\Property(property="total_amount", type="integer", example=50,description="The total amount from the product"),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2025-12-31",description="The expiry date of the product"),
 *     @OA\Property(property="category", type="string", example="Smartphones",description="The category of the product"),
 * )
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        $currentDate = Carbon::now();
        $likes = $this->expressions->where('action', 'like')->count();
        $dislikes = $this->expressions->where('action', 'dislike')->count();
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

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'price' => (float) $this->price,
            'description' => $this->description,
            'current_price' => (float) $currentPrice,
            'user' => $this->user->name,
            'offers' => $this->offers->filter(function ($offer) {
                return Carbon::parse($offer->end_date)->isFuture() || Carbon::parse($offer->end_date)->isToday();
            })->map(function ($offer) {
                return [
                    'discount' => number_format($offer->discount_percentage, 2, '.', '').' %',
                    'starting_at' => Carbon::parse($offer->start_date)->format('Y-n-j'),
                    'ending_at' => Carbon::parse($offer->end_date)->format('Y-n-j'),
                ];
            }),
            'expressions' => [
                'views' => (int) $this->expressions->where('product_id', $this->id)->count(),
                'likes' => (int) $likes,
                'dislikes' => (int) $dislikes,
                'total_expressions' => (int) $likes + $dislikes,
            ],
            'total_amount' => (float) $this->warehouses->sum('amount'),
            'expiry_date' => $minExpiryDate ? $minExpiryDate->format('Y-n-j') : null,
            'category' => $this->category->name,
            'comments' => $this->comments->count(), // Total number of comments
            'average_rating' => $this->reviews->avg('rating') ?: 0, // Average rating, defaulting to 0 if none
            // 'created_at' => $this->created_at->format('Y-n-j'),
        ];

        if ($request->routeIs('products.show')) {
            $data['reviewers'] = $this->reviews->map(function ($review) {
                return [
                    'name' => $review->user->name,
                    'rating' => $review->rating,
                    'comment' => $review->comment ? [
                        'id' => $review->comment->id,
                        'text' => $review->comment->text ?? null,
                        'image' => $review->comment->image ?? null,
                    ] : null,
                ];
            });
        }

        return $data;
    }
}
