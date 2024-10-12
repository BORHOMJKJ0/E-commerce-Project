<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     title="User Resource",
 *     description="User details",
 *
 *     @OA\Property(property="id", type="integer", example=1,description="The ID of the user"),
 *     @OA\Property(property="first_name", type="string", example="John",description="The first name of the user"),
 *     @OA\Property(property="Llast_name", type="string", example="Doe", nullable=true,description="The last name of the user"),
 *     @OA\Property(property="email", type="string", example="john.doe@example.com",description="The email of the user"),
 *     @OA\Property(property="mobile", type="string", example="+1234567890",description="The Phone number of the user"),
 *     @OA\Property(property="address", type="string", example="123 Main St, Anytown, USA", nullable=true,description="The Address of the user"),
 * )
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'address' => $this->address,
        ];
    }
}
