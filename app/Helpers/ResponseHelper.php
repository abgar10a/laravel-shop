<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function successData(string $message, $data = null, int $status = 200): JsonResponse
    {
        unset($data['message']);

        return response()->json(array_filter([
            'message' => $message,
            'error' => false,
            'data' => $data !== null ? $data : null
        ], fn($value) => $value !== null), $status);
    }

    public static function success(string $message, int $status = 200): JsonResponse
    {
        return self::successData($message, null, $status);
    }

    public static function error(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'error' => true,
        ], $status);
    }

    public static function build(string $message = null, array $data = [], string $error = null){
        if ($message) {
            return [
                'message' => $message,
                ...$data,
            ];
        } else {
            return [
                'error' => $error ?: 'Something went wrong',
            ];
        }
    }
}
