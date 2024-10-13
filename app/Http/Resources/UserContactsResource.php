<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="User ContactsResource",
 *     type="object",
 *     title="User Contacts Resource",
 *     description="User Contacts Resource",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the user"),
 *     @OA\Property(property="first_name", type="string", example="John", description="The first name of the user"),
 *     @OA\Property(property="last_name", type="string", example="Doe", description="The last name of the user"),
 *     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com", description="The email address of the user"),
 *     @OA\Property(property="address", type="string", example="123 Main St", description="The address of the user"),
 *     @OA\Property(property="mobile", type="string", example="123456789", description="The mobile number of the user"),
 *     @OA\Property(property="contact_count", type="integer", example=3, description="The count of user contacts"),
 *     @OA\Property(
 *         property="contacts",
 *         type="array",
 *         description="The list of contacts associated with the user",
 *
 *         @OA\Items(ref="#/components/schemas/ContactResource")
 *     ),
 * )
 */
class UserContactsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'address' => $this->address,
            'mobile' => $this->mobile,
            'contact_count' => $this->whenCounted('contacts'),
            'contacts' => $this->when($this->contacts->isNotEmpty(), ContactResource::collection($this->contacts)),
        ];
    }
}
