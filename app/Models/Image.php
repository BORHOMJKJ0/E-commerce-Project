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
 *     @OA\Property(property="image", type="string", example="https://example.com/image.jpg", description="The URL of the image"),
 *     @OA\Property(property="product_id", type="integer", example=1, description="The ID of the product that the image belongs to"),
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
