<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'client_id',
        'date',
        'invoice_number',
        'payment_method',
        'status',
        'document_path'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the customer who owns the invoice.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the line items (articles) for this invoice.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(InvoiceArticle::class, 'invoice_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(CustomerClient::class, 'client_id', 'id');
    }
}
