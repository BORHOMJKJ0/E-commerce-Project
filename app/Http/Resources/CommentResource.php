<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{

    /**
     * @OA\Schema(
     *     schema="CommentResource",
     *     type="object",
     *     title="Comment Resource",
     *     description="Comment details",
     *     @OA\Property(property="id", type="integer", example=42, description="ID of the comment"),
     *     @OA\Property(property="review_id", type="integer", example=15, description="ID of the review"),
     *     @OA\Property(property="product_id", type="integer", example=101, description="ID of the product"),
     *     @OA\Property(property="product_name", type="string", example="iPhone 15", description="Name of the product"),
     *     @OA\Property(property="user_name", type="string", example="John Doe", description="Name of the user who left the comment"),
     *     @OA\Property(property="text", type="string", nullable=true, example="This is a great product!", description="Comment text"),
     *     @OA\Property(property="image", type="string", nullable=true, example="http://example.com/image.jpg", description="Image URL attached to the comment")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'review_id' => $this->review->id,
            'product_id' => $this->review->product->id,
            //            'user_id' => $this->review->user->id,
            'product_name' => $this->review->product->name,
            'user_name' => $this->review->user->name,
            'text' => $this->text ?? null,
            'image' => $this->image ?? null,
        ];
    }
}
