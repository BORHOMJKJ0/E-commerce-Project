<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Expression",
 *     title="Expression Model",
 *     description="Expression details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the expression"),
 *     @OA\Property(property="user_id", type="integer", example=1, description="The ID of the user associated with the expression"),
 *     @OA\Property(property="product_id", type="integer", example=1, description="The ID of the product associated with the expression"),
 *     @OA\Property(property="action", type="string", example="Like", description="the expression"),
 * )
 */
class Expression extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
