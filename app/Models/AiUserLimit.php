<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUserLimit extends Model
{
    protected $fillable = [
        'user_id',
        'daily_request_limit',
        'monthly_token_limit',
        'last_request_at',
        'is_blocked',
    ];

    protected $casts = [
        'last_request_at' => 'datetime',
        'is_blocked' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
}
