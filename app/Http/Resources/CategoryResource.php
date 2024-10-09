<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CategoryResource",
 *     type="object",
 *      title="Category Resource",
 *     description="Category details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the Category"),
 *     @OA\Property(property="name", type="string", example="Electronics",description="The name of the Category"),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         description="Product related to the Category",
 *
 *         @OA\Items(
 *             type="object",
 *
 *             @OA\Property(property="name", type="string", example="Smartphone",description="The name of the product"),
 *             @OA\Property(property="user", type="string", example="Hasan Zaeter",description="The owner of the product")
 *         )
 *     ),
 * )
 */
class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'products' => $this->Products->map(function ($product) {
                $first_name=$product->user->First_Name;
                $last_name=$product->user->Last_Name;
                $full_name = $first_name . ' ' . $last_name;
                return [
                    'name' => $product->name,
                    'user' => trim($full_name),
                ];
            }),
        ];
    }
}
