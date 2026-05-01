<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MobileUserSubscription extends Model
{
    protected $fillable = [
        'customer_id',
        'mobile_user_plan_id',
        'mobile_user_plan_price_id',
        'referral_code_id',
        'billing_cycle',
        'status',
        'price_paid',
        'original_price',
        'referral_discount_amount',
        'currency',
        'starts_at',
        'ends_at',
        'renews_at',
        'canceled_at',
        'trial_ends_at',
        'refund_eligible',
        'payment_provider',
        'provider_customer_id',
        'provider_subscription_id',
    ];

    protected $casts = [
        'price_paid' => 'float',
        'original_price' => 'float',
        'referral_discount_amount' => 'float',
        'refund_eligible' => 'boolean',

        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'renews_at' => 'datetime',
        'canceled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    // 🔗 Relationships
    public function plan()
    {
        return $this->belongsTo(MobileUserPlan::class, 'mobile_user_plan_id');
    }

    public function price()
    {
        return $this->belongsTo(MobileUserPlanPrice::class, 'mobile_user_plan_price_id');
    }

    public function referralCode()
    {
        return $this->belongsTo(ReferralCode::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // 🔥 Helpers
    public function isActive()
    {
        return in_array($this->status, ['active', 'trialing']);
    }

    public function isExpired()
    {
        return $this->ends_at && Carbon::now()->gt($this->ends_at);
    }

    public function isTrial()
    {
        return $this->status === 'trialing';
    }
}
