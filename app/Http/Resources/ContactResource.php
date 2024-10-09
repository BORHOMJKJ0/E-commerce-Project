<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ContactResource",
 *     type="object",
 *     title="Contact Resource",
 *     description="Contact Resource",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the contact"),
 *     @OA\Property(property="link", type="string", example="https://example.com", description="The contact link"),
 *     @OA\Property(property="contact_type_id", type="integer", example=2, description="The contact type ID"),
 * )
 */
class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'link' => $this->link,
            'contact_type_id' => $this->contact_type_id,
        ];
    }
}
