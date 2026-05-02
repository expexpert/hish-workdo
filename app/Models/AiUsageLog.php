<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'channel',
        'model',
        'tokens_in',
        'tokens_out',
        'total_tokens',
        'estimated_cost',
    ];

    public function user()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
}
