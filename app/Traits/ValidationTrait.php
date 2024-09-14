<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

trait ValidationTrait
{
    public function validateRequest($request, $rule): ?JsonResponse
    {
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return null;
    }
}
