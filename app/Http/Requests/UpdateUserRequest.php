<?php

namespace App\Http\Requests;

use App\Rules\MatchOldPassword;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UpdateUserRequest extends BaseRequest
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
            'gender' => 'somtimes|enum|in:male,female',
            'old_password' => ['sometimes', 'string', new MatchOldPassword, 'required_with:new_password'],
            'new_password' => ['sometimes', 'string', 'min:8', 'confirmed', function ($attribute, $value, $fail) {
                if ($this->filled('new_password') && ! $this->filled('old_password')) {
                    $validator = $this->getValidatorInstance();
                    $validator->errors()->add('old_password', 'The old_password field is required.');
                }
            }],
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
