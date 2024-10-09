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
     *
     *     @OA\Property(property="id", type="integer", example=1, description="The ID of the comment"),
     *     @OA\Property(property="review_id", type="integer", example=1, description="The ID of the review"),
     *     @OA\Property(property="product_name", type="string", example="iPhone 15", description="The name of the product"),
     *     @OA\Property(property="user_name", type="string", example="John Doe", description="The full_name of the user who left the comment"),
     *     @OA\Property(property="text", type="string", nullable=true, example="This is a great product!", description="The comment text"),
     *     @OA\Property(property="image", type="string", nullable=true, example="http://example.com/image.jpg", description="The image URL attached to the comment")
     * )
     */
    public function toArray(Request $request): array
    {
        $first_name=$this->review->user->First_Name;
        $last_name=$this->review->user->Last_Name;
        $full_name = $first_name . ' ' . $last_name;
        return [
            'id' => $this->id,
            'review_id' => $this->review->id,
            //'product_id' => $this->review->product->id,
                        'user_id' => $this->review->user->id,
            'product_name' => $this->review->product->name,
            'user_name' => trim($full_name),
            'text' => $this->text ?? null,
            'image' => $this->image ?? null,
        ];
    }
}
