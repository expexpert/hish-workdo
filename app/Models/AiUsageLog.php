<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    protected $fillable = [
        'customer_id',
        'channel',
        'model',
        'tokens_in',
        'tokens_out',
        'total_tokens',
        'estimated_cost',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
