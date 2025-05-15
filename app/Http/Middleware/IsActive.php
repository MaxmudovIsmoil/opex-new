<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return response()->json(data: [
                'success'  => false,
                'error' => 'Not authenticated',
            ], status: 401);
        }


        if (!Auth::user()->status) {
            return response()->json(data: [
                'success' => false,
                'error' => 'You are blocked or invalid username. Contact your administrator.',
            ], status: 403);
        }


        return $next($request);
    }
}
