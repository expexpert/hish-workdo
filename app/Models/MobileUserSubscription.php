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
        'ends_at',
        'renews_at',
        'canceled_at',
        'payment_provider',
        'provider_customer_id',
        'provider_subscription_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'renews_at' => 'datetime',
        'canceled_at' => 'datetime',
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
    | Helpers
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
}
