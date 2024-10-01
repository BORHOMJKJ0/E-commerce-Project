<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserContactsResource extends JsonResource
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
            'email' => $this->email,
            'mobile' => $this->mobile,
            'gender'=>$this->gender,
            'contact_count'=>$this->whenCounted('contacts'),
            'contacts' =>$this->when($this->contacts->isNotEmpty(),ContactResource::collection($this->contacts)),
        ];
    }
}
