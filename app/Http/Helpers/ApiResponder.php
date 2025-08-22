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
            'data' => $data
        ]);
    }

    public static function error(string $message, int $code): JsonResponse
    {
        return response()->json([
            'status_code' => $code,
            'message' => $message,
        ], $code);
    }
}
