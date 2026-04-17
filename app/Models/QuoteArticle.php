<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteArticle extends Model
{
    use HasFactory;

    protected $table = 'quotes_articles';

    protected $fillable = [
        'quotes_id',
        'product_id',
        'designation',
        'unit_price_ht',
        'quantity',
        'total_price_ht',
        'tva_percentage',
    ];

    protected $casts = [
        'unit_price_ht' => 'decimal:2',
        'total_price_ht' => 'decimal:2',
        'tva_percentage' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function quote()
    {
        return $this->belongsTo(CustomerQuote::class, 'quotes_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductService::class);
    }

    /**
     * Get the tax associated with the article.
     */
    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tva_percentage');
    }
}
