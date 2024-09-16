<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Electronics"),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/Product")
 *     ),
 *
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-15 12:30")
 * )
 */
class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
