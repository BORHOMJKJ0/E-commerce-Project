<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Smartphone"),
 *     @OA\Property(property="image", type="string", example="product_image.jpg"),
 *     @OA\Property(property="price", type="number", format="float", example=499.99),
 *     @OA\Property(property="user", type="string", example="John Doe"),
 *     @OA\Property(property="total_amount", type="integer", example=100),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2024-12-31"),
 *     @OA\Property(property="category", type="string", example="Electronics"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-15 12:30")
 * )
 */
class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);

    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    //        public function offers()
    //        {
    //            return $this->hasMany(Offer::class);
    //        }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
