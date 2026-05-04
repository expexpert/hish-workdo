<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReferralCode extends Model
{
    protected $fillable = [
        'code',
        'type',
        'owner_name',
        'owner_customer_id',
        'owner_email',
        'discount_percentage',
        'discount_amount',
        'commission_percentage',
        'commission_fixed_amount',
        'max_uses',
        'clicks',
        'used_count',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'integer',
        'discount_amount' => 'float',
        'commission_percentage' => 'integer',
        'commission_fixed_amount' => 'float',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // 🔗 Relationships
    public function conversions()
    {
        return $this->hasMany(ReferralConversion::class);
    }

    public function owner()
    {
        return $this->belongsTo(Customer::class, 'owner_customer_id');
    }

    // 🔥 Validation Helper
    public function isValid()
    {
        if (!$this->is_active) return false;

        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->ends_at && now()->gt($this->ends_at)) return false;

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;

        return true;
    }
}
