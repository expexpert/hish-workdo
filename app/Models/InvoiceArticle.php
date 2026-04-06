<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CustomerInvoice;

class InvoiceArticle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'invoice_id',
        'designation',
        'unit_price_ht',
        'quantity',
        'total_price_ht',
        'tva_percentage',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'unit_price_ht'  => 'decimal:2',
        'quantity'       => 'integer',
        'total_price_ht' => 'decimal:2',
        'tva_percentage' => 'decimal:2',
    ];

    /**
     * Get the customer invoice that owns the article.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(CustomerInvoice::class, 'invoice_id');
    }
}