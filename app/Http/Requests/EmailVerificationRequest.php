<?php

namespace App\Http\Requests;

class EmailVerificationRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|numeric|digits:6',
            'email' => 'required|email',
        ];
    }
}
