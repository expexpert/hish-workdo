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
        'due_date',
        'invoice_number',
        'payment_method',
        'status',
        'review_status',
        'notes',
        'document_path',
        'is_ocr',
    ];

    protected $casts = [
        'date' => 'date',
        'is_ocr' => 'boolean',
    ];

    protected $appends = ['invoice_url', 'pdf_url'];

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
        return $this->belongsTo(CustomerClient::class, 'client_id', 'id')->withTrashed();
    }
    public function getInvoiceUrlAttribute()
    {
        if (!$this->document_path) {
            return null;
        }

        // Secure API URL instead of the public storage asset URL
        return url("/api/customer/customer-invoices/download/{$this->id}");
    }

    public function getPdfUrlAttribute()
    {
        return url("/api/customer/customer-invoices/pdf/{$this->id}");
    }

    public static function getInvoiceActionStyles($status): string
    {
        return match ($status) {
            'VALIDATED' => 'bg-light text-success border-success',
            'EDIT_REQUESTED' => 'bg-light text-warning border-warning',
            'REJECTED' => 'bg-light text-danger border-danger',
            default => 'bg-white text-muted border-secondary',
        };
    }
}
