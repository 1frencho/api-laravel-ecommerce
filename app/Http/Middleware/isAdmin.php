<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();
        if ($user && $user->role->id == 1) {
            return $next($request);
        } else {
            return response()->json([
                'error' => 'You are not authorized to access this admin resource.',
                'message' => 'Unauthorized',
            ], 403);
        }
        // return $next($request);
    }
}
