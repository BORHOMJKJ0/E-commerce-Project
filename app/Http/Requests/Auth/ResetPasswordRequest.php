<?php

namespace App\Http\Requests\Auth;

use App\Rules\EmailAddress;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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

    public function failedValidation(Validator $validator): void
    {
        response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }
}
