<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;



class ReviewResource extends JsonResource
{

    /**
     * @OA\Schema(
     *     schema="ReviewResource",
     *     type="object",
     *     title="Review Resource",
     *     description="Review details",
     *     
     *     @OA\Property(property="id", type="integer", example=42, description="ID of the review"),
     *     @OA\Property(property="rating", type="number", format="float", example=4.5, description="Rating given by the user"),
     *     
     *     @OA\Property(
     *         property="comment",
     *         type="object",
     *         nullable=true,
     *         description="Comment related to the review",
     *         @OA\Property(property="id", type="integer", example=101, description="ID of the comment"),
     *         @OA\Property(property="text", type="string", nullable=true, example="Great product!", description="Comment text"),
     *         @OA\Property(property="image", type="string", nullable=true, example="http://example.com/image.jpg", description="Image URL attached to the comment")
     *     ),
     *     
     *     @OA\Property(
     *         property="product",
     *         type="object",
     *         description="Details of the reviewed product",
     *         @OA\Property(property="id", type="integer", example=15, description="ID of the product"),
     *         @OA\Property(property="name", type="string", example="iPhone 15", description="Name of the product")
     *     ),
     *     
     *     @OA\Property(
     *         property="user",
     *         type="object",
     *         description="Details of the user who left the review",
     *         @OA\Property(property="id", type="integer", example=5, description="ID of the user"),
     *         @OA\Property(property="name", type="string", example="John Doe", description="Name of the user")
     *     )
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->comment ? [
                'id' => $this->comment->id,
                'text' => $this->comment->text ?? null,
                'image' => $this->comment->image ?? null,
            ] : null,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
