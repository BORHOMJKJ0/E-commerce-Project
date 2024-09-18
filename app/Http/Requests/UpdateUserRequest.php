<?php

namespace App\Http\Requests;

use App\Rules\MatchOldPassword;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'old_password' => ['sometimes', 'string', new MatchOldPassword],
            'new_password' => ['sometimes', 'string', 'min:8', 'confirmed', function ($attribute, $value, $fail) {
                if ($this->filled('new_password') && ! $this->filled('old_password')) {
                    $fail('The old_password field is required ');
                }
            }],
        ];
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
