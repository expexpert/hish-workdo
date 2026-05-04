<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActionLog extends Model
{
    protected $fillable = [
        'admin_user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_status',
        'new_status',
        'note',
    ];

    // 🔗 Relationships
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    // 🔥 Optional helper (polymorphic-like behavior)
    public function entity()
    {
        return match ($this->entity_type) {
            'mobile_user_subscription' => MobileUserSubscription::find($this->entity_id),
            'referral_conversion' => ReferralConversion::find($this->entity_id),
            default => null,
        };
    }
}
