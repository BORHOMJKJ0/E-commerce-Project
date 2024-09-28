<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserContactsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'contacts' => $this->contacts->map(function ($contact) {
                return [
                    'id' => $contact->id,
                    'contact_type_id' => $contact->contact_type_id,
                ];
            }),
        ];
    }
}
