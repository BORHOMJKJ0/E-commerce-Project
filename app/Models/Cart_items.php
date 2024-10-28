<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Cart_items",
 *     type="object",
 *     title="Cart_items Model",
 *     description="Cart_items details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the cart_item"),
 *     @OA\Property(property="quantity", type="integer", example=50,description="The quantity of the cart_item"),
 *     @OA\Property(property="warehouse_id", type="integer", example=1,description="The warehouse ID of the cart_item"),
 *     @OA\Property(property="cart_id", type="integer", example=1,description="The cart ID to the cart_item"),
 * )
 */
class Cart_items extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
