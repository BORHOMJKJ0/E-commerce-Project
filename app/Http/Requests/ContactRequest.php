<?php

namespace App\Http\Requests;

class ContactRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'link' => 'required|string',
            'contact_type_id' => 'required|integer|exists:contact_types,id',
        ];
    }
}
