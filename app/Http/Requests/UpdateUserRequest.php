<?php

namespace App\Http\Requests;

use App\Rules\MatchOldPassword;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UpdateUserRequest extends FormRequest
{
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
            'name' => 'sometimes|string',
            'mobile' => 'sometimes|string|size:10',
            'old_password' => ['sometimes', 'string', new MatchOldPassword, 'required_with:new_password'],
            'new_password' => ['sometimes', 'string', 'min:8', 'confirmed', function ($attribute, $value, $fail) {
                if ($this->filled('new_password') && ! $this->filled('old_password')) {
                    $fail('The old_password field is required ');
                }
            }],
        ];
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'You are not authorized to modify this profile',
            'success' => false,
        ], 403));
    }

    public function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        if (isset($errors['new_password'])) {
            foreach ($errors['new_password'] as $key => $error) {
                if (str_contains($error, 'The old_password field is required')) {
                    $errors['old_password'][] = $error;

                    unset($errors['new_password'][$key]);
                }
            }

            // Reset the new_password array if it's empty
            if (empty($errors['new_password'])) {
                unset($errors['new_password']);
            }
        }

        throw new ValidationException($validator, response()->json($errors, 400));
    }
}
