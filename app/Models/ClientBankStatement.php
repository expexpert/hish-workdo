<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ClientBankStatement extends Model
{
    protected $table = 'client_bank_statement';

    protected $fillable = ['customer_id', 'file_path', 'month_year',];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
