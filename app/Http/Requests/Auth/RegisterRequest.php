<?php

namespace App\Http\Requests\Auth;

use App\Rules\EmailAddress;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', new EmailAddress],
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'required|in:male,female',
            'mobile' => 'required|string|size:10',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 400);
    }

    //    public function attributes(): array{
    //        return [
    //            'name'=>__('main.name'),
    //            'email'=>__('main.email'),
    //            'mobile'=>__('main.mobile'),
    //            'password'=>__('main.password'),
    //            'password_confirmation'=>__('password_confirmation')
    //        ];
    //    }

    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'gender' => 'Gender',
            'mobile' => 'Mobile',
        ];
    }
}
