<?php

namespace App\Http\Requests\Auth;

use App\Rules\EmailAddress;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ForgetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email', new EmailAddress],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }
}
