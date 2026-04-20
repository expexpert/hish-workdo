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

    protected $appends = ['invoice_url', 'pdf_url'];

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

    public function getInvoiceUrlAttribute()
    {
        if (!$this->document_path) {
            return null;
        }

        // Secure API URL instead of the public storage asset URL
        return url("/api/customer/customer-quotes/download/{$this->id}");
    }

    public function getPdfUrlAttribute()
    {
        return url("/api/customer/customer-quotes/pdf/{$this->id}");
    }
}
