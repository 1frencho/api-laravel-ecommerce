<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isUserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('api')->user()) {
            return $next($request);
        } else {
            return response()->json([
                'error' => 'You are not authorized to access this resource',
                'message' => 'Unauthorized',
            ], 401);
        }
        // return $next($request);
    }
}
