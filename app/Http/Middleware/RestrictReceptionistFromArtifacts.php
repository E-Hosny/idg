<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictReceptionistFromArtifacts
{
    /**
     * Reception staff cannot access lab Items / Evaluations workspace routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role === 'receptionist') {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
