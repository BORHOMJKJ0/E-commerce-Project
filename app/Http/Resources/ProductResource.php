<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
 *         property="image",
 *         type="object",
 *         description="Main image of the product",
 *         @OA\Property(property="id", type="integer", example=1, description="Image ID"),
 *         @OA\Property(property="image", type="string", example="http://127.0.0.1:8000/storage/images/image.png", description="Image URL")
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         description="Additional images related to the product",
 *
 *         @OA\Items(
 *             type="object",
 *
 *             @OA\Property(property="id", type="integer", example=2, description="Image ID"),
 *             @OA\Property(property="image", type="string", example="http://127.0.0.1:8000/storage/images/another_image.png", description="Additional image URL"),
 *         )
 *     ),
 *     @OA\Property(property="price", type="number", format="float", example=499.99, description="The original price of the product"),
 *     @OA\Property(property="current_price", type="number", format="float", example=449.99, description="The discounted price, if applicable"),
 *     @OA\Property(property="user", type="string", example="Hasan Zaeter", description="The owner of the product"),
 *     @OA\Property(
 *         property="best_offer",
 *         type="object",
 *         description="Details of the best available offer",
 *         @OA\Property(property="discount", type="string", example="10.00 %", description="Discount percentage"),
 *         @OA\Property(property="starting_at", type="string", format="date", example="2024-09-01", description="Offer start date"),
 *         @OA\Property(property="ending_at", type="string", format="date", example="2024-12-31", description="Offer end date")
 *     ),
 *     @OA\Property(property="max_expiry_date", type="string", format="date", example="2025-12-31", description="Maximum expiry date among warehouses"),
 *     @OA\Property(property="total_amount", type="integer", example=50, description="Total quantity of product available across warehouses"),
 *     @OA\Property(property="category", type="string", example="Smartphones", description="Category name"),
 *     @OA\Property(property="comments", type="integer", example=5, description="Number of comments on the product"),
 *     @OA\Property(
 *         property="expressions",
 *         type="object",
 *         description="Social expressions like views, likes, dislikes",
 *         @OA\Property(property="views", type="integer", example=120, description="View count"),
 *         @OA\Property(property="likes", type="integer", example=45, description="Like count"),
 *         @OA\Property(property="dislikes", type="integer", example=5, description="Dislike count")
 *     ),
 *     @OA\Property(property="average_rating", type="number", format="float", example=4.5, description="Average rating score"),
 *     @OA\Property(
 *         property="ratings_count",
 *         type="object",
 *         description="Count of ratings by score",
 *         @OA\Property(property="1_star", type="integer", example=10, description="1-star rating count"),
 *         @OA\Property(property="2_star", type="integer", example=5, description="2-star rating count"),
 *         @OA\Property(property="3_star", type="integer", example=20, description="3-star rating count"),
 *         @OA\Property(property="4_star", type="integer", example=30, description="4-star rating count"),
 *         @OA\Property(property="5_star", type="integer", example=50, description="5-star rating count")
 *     ),
 *     @OA\Property(property="reviewers_number", type="integer", example=60, description="Number of reviewers"),
 *     @OA\Property(
 *         property="reviewers",
 *         type="array",
 *         description="List of product reviews",
 *
 *         @OA\Items(
 *             type="object",
 *
 *             @OA\Property(property="name", type="string", example="John Doe", description="Reviewer name"),
 *             @OA\Property(property="rating", type="integer", example=5, description="Rating score"),
 *             @OA\Property(property="comment", type="object", description="Review comment",
 *                 @OA\Property(property="id", type="integer", example=101, description="Comment ID"),
 *                 @OA\Property(property="title", type="string", example="Excellent product!", description="The title of the comment"),
 *                 @OA\Property(property="text", type="string", example="This is a great product!", description="Comment text"),
 *             ),
 *             @OA\Property(property="created_at", type="string", format="date", example="2024-10-25", description="Review creation date")
 *         )
 *     )
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
        $imageUrl = $mainImage && $mainImage->image
            ? config('app.url').'/storage/'.$mainImage->image
            : null;
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $mainImage ? [
                'id' => $mainImage->id,
                'image' => $imageUrl ?? null,
            ] : null,
            'price' => (float) $this->price,
            'isFavorite' => $this->favorites()->where(['user_id' => auth()->id(), 'product_id' => $this->id])->exists() ? 1 : 0,
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
            'reviewers_number' => $this->reviewers->count(),
            'average_rating' => $this->reviews->avg('rating') ?: 0,
        ];

        if ($request->routeIs('products.show')) {
            unset($data['image']);
            $data['ratings_count'] = $ratingsCount;
            $data['images'] = $this->images->map(function ($image) {
                $imageUrl = config('app.url').'/storage/'.$image->image;

                return [
                    'id' => $image->id,
                    'image' => $imageUrl,
                ];
            });
            $data['description'] = $this->description;
            $data['reviewers'] = $this->reviews ? $this->reviews->map(function ($review) {
                return [
                    'name' => $review->user->first_name.' '.$review->user->last_name,
                    'rating' => $review->rating,
                    'comment' => $review->comment ? [
                        'id' => $review->comment->id,
                        'title' => $review->comment->title,
                        'text' => $review->comment->text,
                    ] : null,
                    'created_at' => $review->created_at->format('Y-m-d'),
                ];
            }) : [];
        }

        return $data;
    }
}
