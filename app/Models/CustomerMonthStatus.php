<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerMonthStatus extends Model
{
    protected $fillable = ['customer_id', 'month', 'year', 'status', 'updated_by_user_id'];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Map statuses to Bootstrap CSS classes
     */
    public static function getStatusStyles($status): string
    {
        return match($status) {
            'ON_TRACK'          => 'bg-light text-success border-success',
            'MISSING_DOCUMENTS' => 'bg-light text-danger border-danger',
            'IN_REVIEW'         => 'bg-light text-warning border-warning',
            'CLOSED'            => 'bg-light text-secondary border-secondary',
            default             => 'bg-white text-muted border-secondary',
        };
    }
}
