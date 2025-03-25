<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function success(string $message, $data = null, int $status = 200): JsonResponse
    {
        return response()->json(array_filter([
            'message' => $message,
            'error' => false,
            'data' => $data !== null ? $data : null
        ], fn($value) => $value !== null), $status);
    }

    public static function error(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'error' => true,
        ], $status);
    }
}
