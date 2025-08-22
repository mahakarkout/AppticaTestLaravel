<?php

namespace App\Http\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponder
{
    public static function success($data): JsonResponse
    {
        return response()->json([
            'status_code' => 200,
            'message' => 'ok',
            'data' => $data,
        ]);
    }

    public static function error(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'status_code' => $statusCode,
            'message' => $message,
        ], $statusCode);
    }

    public static function handleFetchResult(array $result): ?JsonResponse
    {
        if (isset($result['error'])) {
            return self::error('API fetch failed', 500);
        }

        return null;
    }
}
