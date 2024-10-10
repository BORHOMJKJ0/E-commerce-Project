<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;
use App\Rules\EmailAddress;

class LoginRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', new EmailAddress, 'exists:users,email'],
            'password' => 'required|min:8',
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'Email does not exist in our records',
        ];
    }
}
