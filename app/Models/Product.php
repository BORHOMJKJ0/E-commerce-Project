<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product Model",
 *     description="Product details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the product"),
 *     @OA\Property(property="name", type="string", example="Smartphone",description="The name of the product"),
 *     @OA\Property(property="price", type="number", format="float", example=499.99,description="The price of the product"),
 *     @OA\Property(property="description", type="string", example="Samsung A30s white color 8Ram 128GB space",description="The description of the product"),
 *     @OA\Property(property="user_id", type="integer", example=1,description="The owner ID of the product"),
 *     @OA\Property(property="category_id", type="integer", example=1,description="The category related to the product")
 * )
 */
class Product extends Model
{
    use HasFactory,Prunable;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expressions()
    {
        return $this->hasMany(Expression::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function comments()
    {
        return $this->hasManyThrough(Comment::class, Review::class);
    }

    public function reviewers()
    {
        return $this->belongsToMany(User::class, 'reviews')
            ->withPivot('rating');
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorite_products', 'product_id', 'user_id')->withTimestamps();
    }

    public function prunable()
    {
        $warehouseProductIds = Warehouse::pluck('product_id')->toArray();

        return static::whereNotIn('id', $warehouseProductIds)
            ->where(function ($query) {
                $query->where('created_at', '<', Carbon::now()->subHour())
                    ->orWhereHas('warehouses', function ($warehouseQuery) {
                        $warehouseQuery->havingRaw('SUM(amount) = 0');
                    })
                    ->orWhereDoesntHave('warehouses', function ($warehouseQuery) {
                        $warehouseQuery->where('expiry_date', '>=', Carbon::now());
                    });
            });
    }
}
