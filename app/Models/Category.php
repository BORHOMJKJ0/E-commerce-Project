<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category Model",
 *     description="Category details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the category"),
 *     @OA\Property(property="name", type="string", example="Electronics",description="The name of the category"),
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
