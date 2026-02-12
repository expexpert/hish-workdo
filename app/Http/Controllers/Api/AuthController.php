<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Utility;
use App\Models\ClientNotification;





class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $customer->createToken('mobile-login')->plainTextToken;
        $isNotification = ClientNotification::where('customer_id', $customer->id)->where('is_read', false)->exists();

        return response()->json([
            'token' => $token,
            'customer' => $customer,
            'has_unread_notifications' => $isNotification
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function ForgotPassword(Request $request): JsonResponse
    {
        // 1. Validation
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|exists:customers,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        Utility::getSMTPDetails(1);

        try {
            $token = Str::random(60);

            // 2. Insert into password_resets (ensure table name is correct for your Laravel version)
            DB::table('password_resets')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );

            // 3. Send Mail
            $settings = Utility::settings();
            Mail::send(
                'auth.customerVerify', // Ensure this view exists
                ['token' => $token, 'email' => $request->email],
                function ($message) use ($request, $settings) {
                    $message->from($settings['mail_username'], $settings['mail_from_name']);
                    $message->to($request->email);
                    $message->subject('Reset Password Notification');
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'We have e-mailed your password reset link!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please try again later.'
                // 'error' => $e->getMessage() // Remove this in production for security
            ], 500);
        }
    }
}
