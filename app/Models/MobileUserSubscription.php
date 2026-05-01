<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileUserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'mobile_user_plan_id',
        'status',
        'starts_at',
        'trial_ends_at',
        'ends_at',
        'renews_at',
        'canceled_at',
        'refund_eligible',
        'payment_provider',
        'provider_customer_id',
        'provider_subscription_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'renews_at' => 'datetime',
        'canceled_at' => 'datetime',
        'refund_eligible' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan()
    {
        return $this->belongsTo(MobileUserPlan::class, 'mobile_user_plan_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isTrial()
    {
        return $this->status === 'trialing';
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    /*
    |--------------------------------------------------------------------------
    | Trial Logic
    |--------------------------------------------------------------------------
    */

    public function isTrialActive()
    {
        return $this->trial_ends_at && now()->lt($this->trial_ends_at);
    }

    public function isTrialEnded()
    {
        return $this->trial_ends_at && now()->gte($this->trial_ends_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Refund Logic
    |--------------------------------------------------------------------------
    */

    public function isRefundEligible()
    {
        return (bool) $this->refund_eligible;
    }

    /*
    |--------------------------------------------------------------------------
    | Combined State Logic (VERY USEFUL)
    |--------------------------------------------------------------------------
    */

    public function isUsable()
    {
        // usable if active OR still in trial
        return $this->isActive() || $this->isTrialActive();
    }
}
