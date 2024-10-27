<?php

namespace App\Http\Requests;

class UpdateExpressionRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => 'sometimes|in:like,dislike',
        ];
    }
}
