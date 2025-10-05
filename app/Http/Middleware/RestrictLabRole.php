<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictLabRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user has 'lab' role
        if ($request->user()->role === 'lab') {
            // Redirect to dashboard with error message
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}