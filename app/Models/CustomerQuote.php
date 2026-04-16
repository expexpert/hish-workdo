<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerQuote extends Model
{
    use HasFactory;

    protected $table = 'customer_quotes';

    protected $fillable = [
        'customer_id',
        'client_id',
        'date',
        'due_date',
        'quote_number',
        'payment_method',
        'status',
        'review_status',
        'notes',
        'document_path',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function client()
    {
        return $this->belongsTo(CustomerClient::class, 'client_id');
    }

    public function articles()
    {
        return $this->hasMany(QuoteArticle::class, 'quotes_id');
    }
}
