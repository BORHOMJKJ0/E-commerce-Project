<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Image",
 *     type="object",
 *     title="Image Model",
 *     description="Image details",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the image"),
 *     @OA\Property(property="image", type="string", example="http://127.0.0.1:8000/storage/images/image.png", description="The URL of the image"),
 *     @OA\Property(property="main", type="boolean", example=1, description="Sign if this is the main image or not"),
 *     @OA\Property(property="product_id", type="integer", example=1, description="The ID of the product that the image belongs to")
 * )
 */
class Image extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
