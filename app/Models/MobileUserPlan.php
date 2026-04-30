<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileUserPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price_monthly',
        'currency',
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
        'price_monthly' => 'float',
        'invoice_limit' => 'integer',
        'quote_limit' => 'integer',
        'expense_limit' => 'integer',
        'receipt_limit' => 'integer',
        'ocr_limit' => 'integer',
        'storage_limit_mb' => 'integer',
        'export_enabled' => 'boolean',
        'whatsapp_bot_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function subscriptions()
    {
        return $this->hasMany(MobileUserSubscription::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'mobile_user_plan_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isUnlimited($field)
    {
        return is_null($this->{$field});
    }
}
