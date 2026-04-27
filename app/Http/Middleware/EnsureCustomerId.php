<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Automatically inject the customer_id from the authenticated user
        // This ensures compatibility between Mobile and Bot requests without double logic.
        if ($request->user() && !$request->has('customer_id')) {
            $request->merge(['customer_id' => $request->user()->id]);
        }

        return $next($request);
    }
}
