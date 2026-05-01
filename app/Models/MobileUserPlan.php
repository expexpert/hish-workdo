<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileUserPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'invoice_limit',
        'quote_limit',
        'expense_limit',
        'receipt_limit',
        'ocr_limit',
        'storage_limit_mb',
        'export_enabled',
        'whatsapp_bot_enabled',
        'is_active',
    ];

    protected $casts = [
        'export_enabled' => 'boolean',
        'whatsapp_bot_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    // 🔗 Relationships
    public function prices()
    {
        return $this->hasMany(MobileUserPlanPrice::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(MobileUserSubscription::class);
    }

    // 🔥 Helper
    public function isUnlimited($feature)
    {
        return is_null($this->{$feature . '_limit'});
    }
}
