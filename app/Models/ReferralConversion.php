<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralConversion extends Model
{
    protected $fillable = [
        'referral_code_id',
        'referred_customer_id',
        'subscription_id',
        'original_price',
        'discount_amount',
        'final_price',
        'commission_amount',
        'currency',
        'status',
        'validated_at',
        'paid_at',
    ];

    protected $casts = [
        'original_price' => 'float',
        'discount_amount' => 'float',
        'final_price' => 'float',
        'commission_amount' => 'float',
        'validated_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // 🔗 Relationships
    public function referralCode()
    {
        return $this->belongsTo(ReferralCode::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'referred_customer_id');
    }

    public function subscription()
    {
        return $this->belongsTo(MobileUserSubscription::class);
    }
}
