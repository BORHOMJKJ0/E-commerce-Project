<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Cart",
 *     type="object",
 *     title="Cart Model",
 *     description="Cart details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the cart"),
 *     @OA\Property(property="user_id", type="integer", example=1,description="The owner ID of this cart"),
 * )
 */
class Cart extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cart_items()
    {
        return $this->hasMany(Cart_items::class);
    }
}
