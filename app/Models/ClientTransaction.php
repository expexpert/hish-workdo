<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;




class ClientTransaction extends Model
{
    protected $table = 'client_transactions';

    protected $fillable = ['type', 'transaction_date', 'amount', 'customer_id', 'account_id', 'category_id', 'description', 'reference', 'attachment_path',];


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function account()
    {
        return $this->belongsTo(BankAccount::class, 'account_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductServiceCategory::class);
    }

    public function bankAccount()
    {
        return $this->hasOne('App\Models\BankAccount', 'id', 'account_id')->first();
    }
}
