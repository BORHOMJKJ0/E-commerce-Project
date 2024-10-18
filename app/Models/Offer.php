<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Offer",
 *     type="object",
 *     title="Offer Model",
 *     description="Offer details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the offer"),
 *     @OA\Property(property="discount_percentage", type="number", format="float", example=15.50,description="The discount percentage of the offer"),
 *     @OA\Property(property="start_date", type="string", format="date", example="2024-09-01",description="The start date of the offer"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2024-12-31",description="The end date of the offer"),
 *     @OA\Property(property="warehouse_id", type="integer", example=1,description="The ID of the product associated with the offer"),
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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function prunable()
    {
        return static::where('end_date', '<', Carbon::now());
    }
}
