<?php

namespace App\Http\Controllers\Auth;


use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Services\AuthService;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request)
    {
        $loginData = $this->authService->login(
            $request->validated()
        );

        if ($loginData === null) {
            return ApiResponse::unauthorized(
                'Invalid credentials.'
            );
        }

        $response = ApiResponse::success(
    (new LoginResource($loginData))->resolve($request),
    'Login successful.'
);

return $response->withCookie(
    cookie(
        'refresh_token',
        $loginData['refresh_token'],
        14 * 24 * 60,
        '/',
        null,
        app()->environment('production'),
        true,
        false,
        'lax'
    )
);
    }

 


    public function logout(Request $request)
{
    $this->authService->logout(
        $request->cookie('refresh_token')
    );

    $response = ApiResponse::success(
        null,
        'Logout successful.'
    );

    return $response->withCookie(
        cookie(
            'refresh_token',
            '',
            -1,
            '/',
            null,
            app()->environment('production'),
            true,
            false,
            'lax'
        )
    );
}

    public function refresh(Request $request)
{
    $refreshData = $this->authService->refresh(
        $request->cookie('refresh_token')
    );

    $response = ApiResponse::success(
    [
        'access_token' => $refreshData['access_token'],
        'token_type' => $refreshData['token_type'],
        'expires_in' => $refreshData['expires_in'],
    ],
    'Token refreshed successfully.'
);

return $response->withCookie(
    cookie(
        'refresh_token',
        $refreshData['refresh_token'],
        14 * 24 * 60,
        '/',
        null,
        app()->environment('production'),
        true,
        false,
        'lax'
    )
);
}
}