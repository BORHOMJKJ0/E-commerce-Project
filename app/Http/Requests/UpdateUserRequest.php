<?php

namespace App\Http\Requests;

use App\Rules\MatchOldPassword;
use Illuminate\Http\Exceptions\HttpResponseException;

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

    public function rules(): array
    {
        return [
            'First_name' => 'sometimes|string|max:255',
            'Last_name' => 'sometimes|string|max:255',
            'Address' => 'sometimes|string',
            'mobile' => 'sometimes|string|size:10',
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
        throw new HttpResponseException(
            response()->json([
                'message' => 'You are not authorized to modify this profile',
                'successful' => false,
            ], 403)
        );
    }
}
