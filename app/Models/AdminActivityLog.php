<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    use HasFactory;

    protected $table = 'admin_activity_logs';

    protected $fillable = [
        'company_id',
        'admin_id',
        'action',
        'model',
        'model_id',
        'created_by',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'json',
    ];

    /**
     * Get the company that owns this activity log
     */
    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    /**
     * Get the admin user who performed the activity
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Log an activity
     */
    public static function logActivity($companyId, $adminId, $action, $model = null, $modelId = null, $changes = null, $createdBy = null)
    {
        return self::create([
            'company_id' => $companyId,
            'admin_id' => $adminId,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'changes' => $changes,
            'created_by' => $createdBy,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }

    /**
     * Get formatted action description
     */
    public function getFormattedActionAttribute()
    {
        $actions = [
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'login' => 'Login',
            'logout' => 'Logout',
            'view' => 'Viewed',
        ];

        return $actions[$this->action] ?? ucfirst($this->action);
    }
}
