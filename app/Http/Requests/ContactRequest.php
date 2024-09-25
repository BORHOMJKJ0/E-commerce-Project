<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'link' => 'required|string',
            'contact_type_id' => 'required|integer',
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
