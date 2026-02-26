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
use Illuminate\Validation\Rules\Password;





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
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|exists:customers,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'User with this email not found.',
                'errors' => $validator->errors()
            ], 422);
        }

        Utility::getSMTPDetails(1);

        try {
            // 1. Generate a 6-digit OTP
            $otp = rand(100000, 999999);

            // 2. Store OTP in password_resets table
            DB::table('password_resets')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => $otp, // We store OTP in the token column
                    'created_at' => Carbon::now()
                ]
            );

            // 3. Send Mail with OTP
            $settings = Utility::settings();
            Mail::send(
                'auth.customerVerify', // In this view, change "Link" to "Code: {{ $token }}"
                ['token' => $otp, 'email' => $request->email],
                function ($message) use ($request, $settings) {
                    $message->from($settings['mail_username'], $settings['mail_from_name']);
                    $message->to($request->email);
                    $message->subject('Your Password Reset OTP');
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'A 6-digit OTP has been sent to your email.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP.'
            ], 500);
        }
    }


    public function resetPasswordWithOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'otp' => 'required|numeric',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 1. Check if OTP is valid and not older than 60 minutes
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.'
            ], 422);
        }

        // 2. Update Customer Password
        $customer = Customer::where('email', $request->email)->first();
        $customer->update([
            'password' => Hash::make($request->password)
        ]);

        // 3. Delete the OTP record so it can't be used again
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully.'
        ], 200);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        // 1. Validation
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                'confirmed',
                Password::min(6)
            ],
        ]);

        $user = $request->user();

        // 2. Check if Current Password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'The provided current password does not match our records.'
            ], 422);
        }

        // 3. Update Password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password updated successfully.'
        ], 200);
    }

    public function lastPasswordUpdate(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'last_password_update' => $user->password_changed_at ? Carbon::parse($user->password_changed_at)->toDateTimeString() : null
        ], 200);
    }
}
