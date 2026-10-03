<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;


class ApiResponse
{

    public static function success($data = null, $message = '', $statusCode = 200): JsonResponse
    {

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,

        ], $statusCode);
    }


    public static function error($message = '', $statusCode = 400): JsonResponse
    {

        return response()->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }
}
