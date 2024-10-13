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
            'First_Name' => 'required',
            'Last_Name' => 'sometimes',
            'email' => 'required',
            'password' => 'required',
            'mobile' => 'required',
            'Address' => 'required',
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
}
