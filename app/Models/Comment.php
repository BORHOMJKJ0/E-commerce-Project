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
 *     @OA\Property(property="title", type="string", example="This is a comment title", nullable=true, description="The comment title. Must provide either 'text' or 'image', or both."),
 *     @OA\Property(property="text", type="string", example="This is a comment text", nullable=true, description="The comment text. Must provide either 'title' or 'image', or both."),
 *     @OA\Property(property="image", type="string", example="http://127.0.0.1:8000/storage/images/comment_image.png", nullable=true, description="the image URL of an optional image. Must provide either 'text' or 'title', or both."),
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
