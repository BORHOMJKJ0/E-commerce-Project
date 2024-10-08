<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;
use App\Rules\EmailAddress;

class RegisterRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'First_Name' => 'required|string|max:255',
            'Last_Name' => 'sometimes|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', new EmailAddress],
            'password' => 'required|string|min:8|confirmed',
            'mobile' => 'required|string|size:10',
            'Address' => 'required|string',
        ];
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
