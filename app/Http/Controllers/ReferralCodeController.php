<?php

namespace App\Http\Controllers;

use App\Models\ReferralCode;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ReferralCodeController extends Controller
{

    public function index()
    {
        if (\Auth::user()->type == 'super admin') {
            $referralCodes = ReferralCode::get();

            return view('ReferralCode.index', compact('referralCodes'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        return view('ReferralCode._form');
    }


    public function store(Request $request)
    {
        // ✅ Normalize FIRST
        $request->merge([
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        // Validation
        $validator = \Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:referral_codes,code',
            'type' => 'required|in:influencer,partner,user',
            'owner_name' => 'nullable|string|max:100',
            'owner_email' => 'nullable|email|max:100',
            'discount_percentage' => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0',
            'max_uses' => 'required|integer|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $referral = new ReferralCode();
        $referral->code = $request->code;
        $referral->type = $request->type;
        $referral->owner_name = $request->owner_name;
        $referral->owner_email = $request->owner_email;
        $referral->discount_percentage = $request->discount_percentage ?? 0;
        $referral->commission_percentage = $request->commission_percentage ?? 0;
        $referral->max_uses = $request->max_uses ?? 0;
        $referral->starts_at = $request->starts_at ?? null;
        $referral->ends_at = $request->ends_at ?? null;
        $referral->clicks = 0;
        $referral->used_count = 0;
        $referral->is_active = $request->is_active;

        $referral->save();

        return redirect()->back()->with('success', __('Referral code created successfully.'));
    }

    public function edit($referralCode)
    {
        $referralCode  = ReferralCode::find($referralCode);

        return view('ReferralCode._form', compact('referralCode'));
    }

    public function update(Request $request, $id)
    {
        $referral = ReferralCode::findOrFail($id);

        // ✅ Normalize FIRST
        $request->merge([
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        $validator = \Validator::make($request->all(), [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('referral_codes', 'code')->ignore($id),
            ],
            'type' => 'required|in:influencer,partner,user',
            'owner_name' => 'nullable|string|max:100',
            'owner_email' => 'nullable|email|max:100',
            'discount_percentage' => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0',
            'max_uses' => 'required|integer|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $referral->code = $request->code;
        $referral->type = $request->type;
        $referral->owner_name = $request->owner_name;
        $referral->owner_email = $request->owner_email;
        $referral->discount_percentage = $request->discount_percentage ?? 0;
        $referral->commission_percentage = $request->commission_percentage ?? 0;
        $referral->max_uses = $request->max_uses ?? 0;
        $referral->starts_at = $request->starts_at ?? null;
        $referral->ends_at = $request->ends_at ?? null;
        $referral->is_active = $request->is_active;

        $referral->save();

        return redirect()->back()->with('success', __('Referral code updated successfully.'));
    }


    public function destroy(ReferralCode $code)
    {
        if ($code) {
            $code->delete();
            return redirect()->route('referral.codes.index')
                ->with('success', __('Referral code successfully deleted.'));
        }

        return redirect()->back()->with('error', __('Permission denied.'));
    }
}
