<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {

        $errors = collect($validator->errors())->map(fn ($message) => $message[0]);

        throw new HttpResponseException(
            ResponseHelper::jsonResponse(
                $errors->toArray(),
                'Validation failed',
                400,
                false
            )
        );
    }
}
