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
use App\Models\User;
use App\Models\ChartOfAccount;
use App\Models\BankAccount;
use App\Models\MobileUserPlan;
use App\Models\MobileUserPlanPrice;
use App\Models\MobileUserSubscription;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;





class AuthController extends Controller
{

    public function register(Request $request)
    {
        $firstAccountant = User::where('type', 'accountant')
            ->orderBy('id', 'asc')
            ->first();

        $createdBy = $firstAccountant?->id ?? 3;


        $latest = Customer::where('created_by', '=', $createdBy)->latest()->first();
        $latestCustomerId = $latest->id + 1 ?? 1;

        // Validation
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('customers')->where(function ($query) use ($createdBy) {
                    return $query->where('created_by', $createdBy);
                }),
            ],
            'contact' => 'required|string|max:20',
            'password' => 'required|string|min:8',

            // Company & billing
            'company_type' => 'required|string|max:255',
            'billing_name' => 'required|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_address' => 'nullable|string|max:255',
            'billing_zip' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',

            // Legal fields
            'ice_number' => 'nullable|string|max:255',
            'patent_number' => 'nullable|string|max:255',
            'rc_number' => 'nullable|string|max:255',
            'cnss' => 'nullable|string|max:255',
            'if_number' => 'nullable|string|max:255',
            'rib' => 'nullable|string|max:255',
            'vat_number' => 'nullable|string|max:255',

            // Files
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        // Handle file uploads
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('signature')) {
            $validated['signature'] = $request->file('signature')->store('signatures', 'public');
        }

        // Create customer
        $customer = Customer::create([
            'name' => $validated['first_name'] . ' ' . ($validated['last_name'] ?? ''),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'contact' => $validated['contact'],

            'created_by' => $createdBy,
            'customer_id' => $latestCustomerId,

            'is_b2c' => true,
            'is_enable_login' => 0,

            // Company & billing
            'company_type' => $validated['company_type'],
            'billing_name' => $validated['billing_name'],
            'billing_city' => $validated['billing_city'],
            'billing_address' => $validated['billing_address'] ?? null,
            'billing_zip' => $validated['billing_zip'] ?? null,
            'website' => $validated['website'] ?? null,

            // Legal
            'ice_number' => $validated['ice_number'] ?? null,
            'patent_number' => $validated['patent_number'] ?? null,
            'rc_number' => $validated['rc_number'] ?? null,
            'cnss' => $validated['cnss'] ?? null,
            'if_number' => $validated['if_number'] ?? null,
            'rib' => $validated['rib'] ?? null,
            'vat_number' => $validated['vat_number'] ?? null,

            // Files
            'avatar' => $validated['avatar'] ?? null,
            'signature' => $validated['signature'] ?? null,
        ]);

        // Generate token
        $token = $customer->createToken('mobile-login')->plainTextToken;


        $freePlan = MobileUserPlan::where('name', 'Free')->first();
        $price = MobileUserPlanPrice::with('plan')->findOrFail($freePlan->id);

        $plan = $price->plan;

        MobileUserSubscription::create([
            'customer_id' => $customer->id,
            'mobile_user_plan_id' => $plan->id,
            'mobile_user_plan_price_id' => $price->id,
            'referral_code_id' => null,
            'billing_cycle' => $price->billing_cycle,
            'status' => 'active',
            'original_price' => $price->price,
            'referral_discount_amount' => 0,
            'price_paid' => $price->price,
            'currency' => $price->currency,
            'refund_status' => 'none',
            'starts_at' => now(),
            'ends_at' => now()->addMonths(1),
            'renews_at' => now()->addMonths(1),
            'trial_ends_at' => now()->addDays(7),
            'payment_provider' => 'test',
        ]);

        $customer->update([
            'mobile_user_plan_id' => $plan->id,
            'subscription_status' => 'active',
            'is_enable_login'  => 1,
        ]);




        $randomStr = Str::random(10);
        $companyID = User::where('id', $createdBy)->pluck('created_by')->first();

        $accounts = [
            ['code' => '5141', 'bank_name' => 'Banque principale'],
            ['code' => '5161', 'bank_name' => 'Caisse'],
        ];

        foreach ($accounts as $acc) {

            $chartOfAccount = ChartOfAccount::where('created_by', $companyID)
                ->where('code', $acc['code'])
                ->latest()
                ->first();

            if (!$chartOfAccount) {
                continue;
            }

            BankAccount::create([
                'chart_account_id' => $chartOfAccount->id,
                'customer_id'      => $customer->id,
                'holder_name'      => $customer->name,
                'bank_name'        => $acc['bank_name'],
                'account_number'   => $randomStr,
                'opening_balance'  => 0,
                'contact_number'   => $customer->contact,
                'created_by'       => $companyID,
            ]);
        }


        $userpassword  = $validated['password'];
        $role_r = Role::where('name', '=', 'customer')->firstOrFail();
        $customer->assignRole($role_r);

        $uArr = [
            'email' => $customer->email,
            'password' => $userpassword,
        ];

        try {
            $resp = Utility::sendEmailTemplate('user_created', [$customer->id => $customer->email], $uArr);
        } catch (\Exception $e) {
            $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
        }


        //Twilio Notification
        $setting  = Utility::settings($companyID);
        if (isset($setting['customer_notification']) && $setting['customer_notification'] == 1) {
            $uArr = [
                'customer_name' => $request->name,
                'email'  => $request->email,
                'password'  =>  $userpassword,
            ];
            Utility::send_twilio_msg($request->contact, 'new_customer', $uArr);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'customer' => $customer
        ], 201);
    }

    public function checkEmail(Request $request)
    {
        $firstAccountant = User::where('type', 'accountant')
            ->oldest('id')
            ->first();
        $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('customers')->where(function ($query) use ($request, $firstAccountant) {
                    return $query->where('created_by', $firstAccountant ? $firstAccountant->id : '1');
                }),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email is available'
        ]);
    }


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

        // if ($customer->is_b2c == true && $customer->is_enable_login == 0) {
        //     return response()->json([
        //         'message' => 'Your account is not enabled for app access. Please contact support.'
        //     ], 403);
        // }

        $token = $customer->createToken('mobile-login')->plainTextToken;
        $customer->update([
            'last_login_at' => Carbon::now(),
        ]);
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
