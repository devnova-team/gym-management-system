<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnerMiddleware
{
    
    public function handle(Request $request, Closure $next): Response


    {

           if ($request->user()->role !== 'owner') {
            return ApiResponse::forbidden(
                'You do not have permission to perform this action.'
            );
        }

        return $next($request);
    }
}
