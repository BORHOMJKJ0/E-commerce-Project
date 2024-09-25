<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

/**
 * @OA\Schema(
 *     schema="Offer",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="discount_percentage", type="number", format="float", example=15.50),
 *     @OA\Property(property="start_date", type="string", format="date", example="2024-09-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2024-12-31"),
 *     @OA\Property(property="product_id", type="integer", example=1),
 * )
 */
class Offer extends Model
{
    use HasFactory,Prunable;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function prunable()
    {
        return static::where('end_date', '<', Carbon::now());
    }
}
