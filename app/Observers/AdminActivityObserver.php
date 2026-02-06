<?php

namespace App\Observers;

use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Model;

class AdminActivityObserver
{
    /**
     * Check if super admin is currently impersonating
     */
    private function isAdminImpersonating(&$adminId = null)
    {
        $adminId = session('admin_impersonating_company_id');
        
        if (!$adminId) {
            return false;
        }
        
        $admin = \App\Models\User::find($adminId);
        return $admin && $admin->type === 'super admin';
    }

    /**
     * Handle the model "created" event.
     */
    public function created(Model $model)
    {
        if (!$this->isAdminImpersonating($adminId)) {
            return;
        }

        // Get the current authenticated user (the company)
        $company = \Auth::user();
        if (!$company) {
            return;
        }

        $changes = $this->formatchanges($model->toArray());

        AdminActivityLog::logActivity(
            $company->id,
            $adminId,
            'create',
            class_basename($model),
            $model->id,
            json_encode($changes),
            $adminId
        );
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated(Model $model)
    {
        if (!$this->isAdminImpersonating($adminId)) {
            return;
        }

        // Get the current authenticated user (the company)
        $company = \Auth::user();
        if (!$company) {
            return;
        }

        // Get the changes
        $changes = [];
        foreach ($model->getChanges() as $key => $newValue) {
            // Skip logging sensitive fields and timestamps
            if (in_array($key, ['password', 'remember_token', 'email_verified_at', 'updated_at'])) {
                continue;
            }

            $oldValue = $model->getOriginal($key);
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (empty($changes)) {
            return;
        }

        AdminActivityLog::logActivity(
            $company->id,
            $adminId,
            'update',
            class_basename($model),
            $model->id,
            json_encode($changes),
            $adminId
        );
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted(Model $model)
    {
        if (!$this->isAdminImpersonating($adminId)) {
            return;
        }

        // Get the current authenticated user (the company)
        $company = \Auth::user();
        if (!$company) {
            return;
        }

        $changes = $this->formatchanges($model->toArray());

        AdminActivityLog::logActivity(
            $company->id,
            $adminId,
            'delete',
            class_basename($model),
            $model->id,
            json_encode(['deleted_data' => $changes]),
            $adminId
        );
    }

    /**
     * Format changes to exclude sensitive data
     */
    private function formatchanges($data)
    {
        $sensitiveFields = ['password', 'remember_token', 'api_token', 'secret'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }

        return $data;
    }
}


