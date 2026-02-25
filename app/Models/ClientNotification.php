<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientNotification extends Model
{
    protected $table = 'client_notifications';
    protected $appends = ['document_url'];

    protected $fillable = [
        'customer_id',
        'sender_id',
        'title',
        'message',
        'is_read',
        'data',
        'document',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getDocumentUrlAttribute()
    {
        if (!$this->document) {
            return null;
        }
    
        return asset('storage/' . $this->document);
    }
}
