<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; 

class CustomerClient extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_clients';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'company_name',
        'client_name',
        'email',
        'telephone',
        'postal_code',
        'city',
        'commercial_register',
        'ice',
    ];

    /**
     * Get the customer that owns the client.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the invoices for this client.
     */
    public function invoices()
    {
        return $this->hasMany(CustomerInvoice::class, 'client_id');
    }

    public function articles()
    {
        return $this->hasManyThrough(InvoiceArticle::class, CustomerInvoice::class, 'client_id', 'invoice_id');
    }
}
