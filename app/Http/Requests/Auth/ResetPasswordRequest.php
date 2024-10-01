<?php

namespace App\Http\Requests\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Requests\BaseRequest;
use App\Rules\EmailAddress;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email', new EmailAddress],
            'password' => 'required|min:8|confirmed',
            'code' => 'required|max:6',
        ];
    }
}
