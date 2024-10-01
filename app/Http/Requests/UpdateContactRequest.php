<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userIdFormRoute = $this->route('user_id');
        if ($userIdFormRoute != auth()->user()->id) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'link' => 'sometimes|string',
            'contact_type_id' => 'sometimes|exists:contact_types,id',
        ];
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException(response: response()->json([
            'message' => 'You are not authorized to modify this profile',
            'success' => false,
        ], 403));
    }
    
}
