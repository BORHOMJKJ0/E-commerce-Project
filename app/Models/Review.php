<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Review",
 *     title="Review Model",
 *     description="Review details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the review"),
 *     @OA\Property(property="user_id", type="integer", example=1, description="The ID of the user who made the review"),
 *     @OA\Property(property="product_id", type="integer", example=1, description="The ID of the product being reviewed"),
 *     @OA\Property(property="rating", type="integer", example=5, description="Rating given by the user"),
 *   )
 */
class Review extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function comment()
    {
        return $this->hasOne(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
