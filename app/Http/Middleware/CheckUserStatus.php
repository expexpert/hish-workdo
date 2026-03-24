<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in AND is_enable_login is 0
        if (Auth::check() && Auth::user()->is_enable_login == 0) {

            // Log the user out
            Auth::logout();

            // Clear session to prevent any loop or stale data
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Your account is disabled.');
        }

        return $next($request);
    }
}
