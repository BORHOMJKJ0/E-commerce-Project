<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Comment",
 *     title="Comment Model",
 *     description="Comment details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the comment"),
 *     @OA\Property(property="review_id", type="integer", example=1, description="The ID of the associated review"),
 *     @OA\Property(property="text", type="string", example="This is a comment", nullable=true, description="The comment text. Must provide either 'text' or 'image', or both."),
 *     @OA\Property(property="image", type="string", example="http://example.com/image.jpg", nullable=true, description="the image URL of an optional image. Must provide either 'text' or 'image', or both."),
 * )
 */
class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
}
