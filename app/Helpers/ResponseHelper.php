<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function jsonResponse($data = null, string $message = '', int $statusCode = 200, bool $successful = true): JsonResponse
    {
        $responseData = [
            'successful' => $successful,
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode,
        ];

        if (is_null($data) || (is_array($data) && empty($data))) {
            unset($responseData['data']);
        }

        return response()->json($responseData, $statusCode);
    }
}
