<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerExpense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'customer_id',
        'supplier_id',
        'category_id',
        'file',
        'date',
        'ttc',
        'tva',
        'payment_method',
        'total_ttc',
        'total_tva',
        'notes',
        'reference',
        'is_ocr',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'date' => 'date',
        'ttc' => 'decimal:2',
        'tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'total_tva' => 'decimal:2',
    ];

    protected $appends = ['file_url'];
    /**
     * Get the customer that owns the expense.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the category associated with the expense.
     */
    public function category(): BelongsTo
    {
        // Explicitly defining the foreign key if it differs from the table name
        return $this->belongsTo(CustomerCategory::class, 'category_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(CustomerSupplier::class, 'supplier_id')->withTrashed();
    }

    public function getFileUrlAttribute()
    {
        if (!$this->file) {
            return null;
        }

        return url("/api/customer/customer-expenses/file/{$this->id}");
    }
}