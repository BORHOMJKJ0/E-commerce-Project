<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'First_Name' => $this->First_Name,
            'Last_Name' => $this->Last_Name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'Address' => $this->Address,
        ];
    }
}
