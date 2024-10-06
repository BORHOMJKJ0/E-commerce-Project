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
            'First_Name' => $this->First_Name,
            'Last_Name' => $this->Last_Name,
            'email' => $this->email,
            'Address'=>$this->Address,
            'mobile' => $this->mobile,
            'contact_count' => $this->whenCounted('contacts'),
            'contacts' => $this->when($this->contacts->isNotEmpty(), ContactResource::collection($this->contacts)),
        ];
    }
}
