<?php


use App\Helpers\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

           $middleware->alias([
        'owner' => \App\Http\Middleware\OwnerMiddleware::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Validation errors
        $exceptions->render(function (
            ValidationException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::validationError(
                    $e->errors()
                );
            }
        });

                    // Authentication errors
            $exceptions->render(function (
                AuthenticationException $e,
                Request $request
            ) {
                if ($request->is('api/*')) {
                    return ApiResponse::unauthorized(
                        'Unauthenticated.'
                    );
                }
            });

            // Resource not found
            $exceptions->render(function (
                ModelNotFoundException $e,
                Request $request
            ) {
                if ($request->is('api/*')) {
                    return ApiResponse::error(
                        'Resource not found.',
                        404
                    );
                }
            });

        // Rate limit errors
        $exceptions->render(function (
            ThrottleRequestsException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                $retryAfter = (int) ($e->getHeaders()['Retry-After'] ?? 60);

                return response()->json([
                    'success' => false,
                    'message' => 'Too many login attempts. Please try again later.',
                    'retry_after' => $retryAfter,
                ], 429);
            }
        });

        // JWT blacklisted token
        $exceptions->render(function (
            TokenBlacklistedException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::unauthorized(
                    'Token has been blacklisted.'
                );
            }
        });

        // JWT errors
        $exceptions->render(function (
            JWTException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::unauthorized(
                    'Invalid token.'
                );
            }
        });

       $exceptions->render(function (
    RuntimeException $e,
    Request $request
) {
    if ($request->is('api/*')) {
        if (in_array($e->getMessage(), [
            'Refresh token is missing.',
            'Invalid or expired refresh token.',
        ])) {
            return ApiResponse::unauthorized(
                $e->getMessage()
            );
        }

        if ($e->getMessage() === 'Cannot delete a plan with active subscriptions.') {
            return ApiResponse::error(
                $e->getMessage(),
                422
            );
        }
    }
});

    $exceptions->render(function (
    NotFoundHttpException $e,
    Request $request
) {
    if ($request->is('api/*')) {
        return ApiResponse::error(
            'Resource not found.',
            404
        );
    }
});
    })

    ->create();
