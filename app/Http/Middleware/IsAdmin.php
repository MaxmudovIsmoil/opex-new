<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return response()->json([
                'success'  => false,
                'error' => 'no auth check',
            ]);
        }

        if (Auth::user()->rule !== "ADMIN") {
            return response()->json([
                'success'  => false,
                'error' => 'Sorry, you do not have permission to access this menu.',
            ]);
        }

        return $next($request);
    }
}
