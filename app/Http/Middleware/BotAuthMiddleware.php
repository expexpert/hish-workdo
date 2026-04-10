<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BotAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('ai_limits.bot_secret');

        // Force JSON response to prevent any redirects
        $request->headers->set('Accept', 'application/json');

        if (!$request->header('X-Bot-Secret') || $request->header('X-Bot-Secret') !== $secret) {
            Log::warning('❌ BotAuthMiddleware: Unauthorized Bot Access Attempt', [
                'received_secret' => $request->header('X-Bot-Secret'),
                'expected_secret' => $secret
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized Bot Access'
            ], 401);
        }

        // --- NEW: Sudo-Bot Impersonation ---
        // If a phone header is provided, we "log in" as that customer for this request.
        $phone = $request->header('X-Customer-Phone');
        if ($phone) {
            $customer = \App\Models\Customer::where('contact', 'like', "%$phone%")
                ->where('bot_active', true)
                ->first();
            if ($customer) {
                Log::info('🛡️ BotAuthMiddleware: Authed User IDENTIFIED', ['phone' => $phone]);
                Auth::setUser($customer);
            } else {
                Log::warning('⚠️ BotAuthMiddleware: User NOT FOUND or NOT ACTIVE', ['phone' => $phone]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Customer not found or bot not active for this number.'
                ], 403);
            }
        } else {
            // For /bot/customer routes, phone header is MANDATORY
            if ($request->is('api/bot/customer/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'X-Customer-Phone header is required for bot requests.'
                ], 400);
            }
        }

        return $next($request);
    }
}
