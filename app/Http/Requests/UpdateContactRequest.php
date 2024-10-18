<?php

namespace App\Http\Requests;

class UpdateContactRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $userIdFormRoute = $this->route('user_id');
        if ($userIdFormRoute != auth()->user()->id) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'link' => 'sometimes|string',
            'contact_type_id' => 'sometimes|exists:contact_types,id',
        ];
    }
}
