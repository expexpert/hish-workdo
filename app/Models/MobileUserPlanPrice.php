<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileUserPlanPrice extends Model
{
    protected $fillable = [
        'mobile_user_plan_id',
        'billing_cycle',
        'price',
        'currency',
        'discount_percentage',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'discount_percentage' => 'integer',
        'is_active' => 'boolean',
    ];

    // 🔗 Relationships
    public function plan()
    {
        return $this->belongsTo(MobileUserPlan::class, 'mobile_user_plan_id');
    }
}
