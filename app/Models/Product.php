<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Smartphone"),
 *     @OA\Property(property="image", type="string", example="product_image.jpg"),
 *     @OA\Property(property="price", type="number", format="float", example=499.99),
 *     @OA\Property(property="description", type="string", example="Samsung A30s white color 8Ram 128GB space"),
 *     @OA\Property(property="current_price", type="number", format="float", example=422.49),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(
 *         property="offers",
 *         type="array",
 *
 *         @OA\Items(
 *
 *             @OA\Property(property="discount", type="number", format="float", example=15.50),
 *             @OA\Property(property="starting_at", type="string", format="date-time", example="2024-09-21 23:20"),
 *             @OA\Property(property="ending_at", type="string", format="date-time", example="2024-09-21 23:20")
 *         )
 *     ),
 *     @OA\Property(property="total_amount", type="integer", example=100),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2024-12-01"),
 *     @OA\Property(property="category_id", type="integer", example=1)
 * )
 */
class Product extends Model
{
    use HasFactory,Prunable;

    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expressions(): HasMany
    {
        return $this->hasMany(Expression::class);
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
