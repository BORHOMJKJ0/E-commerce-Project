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
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="pure_price", type="number", format="float", example=300.50),
 *     @OA\Property(property="amount", type="number", format="integer", example="100"),
 *     @OA\Property(property="payment_date", type="string", format="date", example="2024-09-01"),
 *     @OA\Property(property="settlement_date", type="string", format="date", example=null),
 *     @OA\Property(property="expiry_date", type="string", format="date", example="2024-12-01"),
 *     @OA\Property(property="product_id",type="integer",example=1),
 * )
 */
class Warehouse extends Model
{
    use HasFactory,Prunable;

    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'date',
        'settlement_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function prunable()
    {
        return static::where('expiry_date', '<', Carbon::now())
            ->orWhere('amount', '=', 0);
    }
}
