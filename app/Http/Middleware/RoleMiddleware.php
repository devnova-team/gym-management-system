<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    
    public function handle(Request $request, Closure $next,string ...$roles): Response
    {
        $user = $request->user();
         if (!$user || !in_array($user->role, $roles, true)) {
            return ApiResponse::forbidden(
                'You do not have permission to perform this action.'
            );
        }
        return $next($request);
    }
}
