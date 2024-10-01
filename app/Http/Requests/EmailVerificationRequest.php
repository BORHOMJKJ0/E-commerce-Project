<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
//use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

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
            'email' => 'required|email|exists:users,email',
        ];
    }
    
}
