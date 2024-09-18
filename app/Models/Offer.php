<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Offer",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Smartphone"),
 *     @OA\Property(property="image", type="string", example="product_image.jpg"),
 *     @OA\Property(property="price", type="number", format="float", example=499.99),
 *     @OA\Property(property="description", type="string", example="Samsung A30s white color 8Ram 128GB space"),
 *     @OA\Property(property="user", type="string", example="John Doe"),
 *     @OA\Property(property="total_amount", type="integer", example=100),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2024-12-31"),
 *     @OA\Property(property="category", type="string", example="Electronics"),
 * )
 */
class Offer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
