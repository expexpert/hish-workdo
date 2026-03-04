<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProduct extends Model
{
    use HasFactory;

    protected $table = 'customers_products';

    protected $fillable = [
        'customer_id',
        'designation',
        'unit_price_ht',
        'tva_percent',
        'quantity',
        'total_price_ht',
    ];

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
