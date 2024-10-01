<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function jsonRespones(array $data = [], string $message = '', int $statusCode = 200, bool $successful = true): JsonResponse
    {
        return response()->json([
            'successful' => $successful,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}
