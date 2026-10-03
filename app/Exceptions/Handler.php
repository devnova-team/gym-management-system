<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Helpers\ApiResponse;


class Handler extends ExceptionHandler
{

    public function render($request, Throwable $e)
    {

        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return ApiResponse::error($e->errors(), 422);
        }

        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return ApiResponse::error('Unauthenticated', 401);
        }


        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return ApiResponse::error('Resource not found', 404);
        }

        if (app()->environment('local')) {
            return ApiResponse::error($e->getMessage(), 500);
        }

        return ApiResponse::error('Internal Server Error', 500);
    }
}
