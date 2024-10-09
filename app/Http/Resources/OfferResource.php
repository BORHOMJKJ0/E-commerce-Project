<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OfferResource",
 *     type="object",
 *     title="Offer Resource",
 *     description="Offer details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the offer"),
 *     @OA\Property(property="discount_percentage", type="number", format="float", example=10.00,description="The discount percentage of the offer"),
 *     @OA\Property(property="start_date", type="string", format="date", example="2024-09-01",description="The start date of the offer"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2024-12-31",description="The end date of the offer"),
 *     @OA\Property(property="product", type="object",description="Product Detalis related to the offer",
 *         @OA\Property(property="name", type="string", example="Iphone 15",description="The name of the product"),
 *         @OA\Property(property="price", type="number", format="float", example=499.99,description="The price of the product"),
 *         @OA\Property(property="description", type="string", example="A high-end smartphone with excellent features and a sleek design",description="The description of the product"),
 *         @OA\Property(property="category", type="string", example="Smartphone",description="The category of the product"),
 *         @OA\Property(property="user", type="string", example="Hasan Zaeter",description="The owner of the product"),
 *     ),
 * )
 */
class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $first_name=$this->Product->User->First_Name;
        $last_name=$this->Product->User->Last_Name;
        $full_name = $first_name . ' ' . $last_name;
        return [
            'id' => $this->id,
            'discount_percentage' => number_format($this->discount_percentage, 2, '.', '').' %',
            'start_date' => $this->start_date ? $this->start_date->format('Y-n-j') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-n-j') : null,
            'product' => $this->Product ? [
                'name' => $this->Product->name,
                'price' => (float) $this->Product->price,
                'description' => $this->Product->description,
                'category' => $this->Product->Category ? $this->Product->Category->name : null,
                'user' => $this->Product->User ? $this->Product->User->$full_name : null,
            ] : null,
        ];
    }
}
