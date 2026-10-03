<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Operation successful.',
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function error(
        string $message,
        int $statusCode
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    public static function validationError(
        mixed $errors,
        string $message = 'Validation failed.'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    public static function unauthorized(
        string $message = 'Unauthorized.'
    ): JsonResponse {
        return self::error($message, 401);
    }

    public static function forbidden(
        string $message = 'You do not have permission to perform this action.'
    ): JsonResponse {
        return self::error($message, 403);
    }
}