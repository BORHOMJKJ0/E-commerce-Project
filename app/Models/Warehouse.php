<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Warehouse",
 *     type="object",
 *     title="Warehouse Model",
 *     description="Warehouse details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the warehouse"),
 *     @OA\Property(property="amount", type="number", format="integer", example="100",description="The amount of the warehouse"),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2024-12-01",description="The expiry date of the warehouse"),
 *     @OA\Property(property="product_id",type="integer",example=1,description="The product ID related to the warehouse"),
 * )
 */
class Warehouse extends Model
{
    use HasFactory,Prunable;

    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function prunable()
    {
        return static::where('expiry_date', '<', Carbon::now())
            ->orWhere('amount', '=', 0);
    }
}
