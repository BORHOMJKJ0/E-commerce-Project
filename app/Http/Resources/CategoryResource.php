<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CategoryResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Electronics"),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *
 *         @OA\Items(ref="#/components/schemas/ProductResource")
 *     ),
 *
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-09-15 12:30")
 * )
 */
class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'products' => $this->Products->map(function ($product) {
                return [
                    'name' => $product->name,
                    'user' => $product->user->name,
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
