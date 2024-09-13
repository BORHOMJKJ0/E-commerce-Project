<?php

namespace App\Http\Requests\Auth;

use App\Rules\CheckEmail;
use App\Rules\EmailAddress;
use App\Traits\failedValidationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Js;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email'=>['required', 'string', 'email', 'max:255', 'unique:users,email',new EmailAddress],
            'password' => 'required|string|min:8|confirmed',
            'gender'=>'required|in:male,female',
            'mobile'=>'required|string|size:10|unique:users,mobile',
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

      public function attributes(): array{
        return[
            'name'=>'Name',
            'email'=>'Email',
            'password'=>'Password',
            'gender'=>'Gender',
            'mobile'=>'Mobile',
        ];
      }

}
