<?php

namespace App\Http\Middleware;

use App\Models\HiddenQoyodCustomer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureQoyodCustomerVisible
{
    public function handle(Request $request, Closure $next): Response
    {
        $customerId = $request->route('customer');

        if ($customerId && HiddenQoyodCustomer::isHidden($customerId) && ! $request->user()?->isAdmin()) {
            return redirect()->route('dashboard.customers')
                ->withErrors(['error' => 'Customer not found.']);
        }

        return $next($request);
    }
}
