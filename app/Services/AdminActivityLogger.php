<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;

class AdminActivityLogger
{
    /**
     * Log an admin activity
     */
    public static function log($action, $model = null, $modelId = null, $changes = null, $companyId = null)
    {
        // Check if the current user is a super admin impersonating a company
        $admin = Auth::user();
        $adminId = session('admin_impersonating_company_id');
        
        if (!$admin || !$adminId) {
            return null;
        }

        $originalAdmin = \App\Models\User::find($adminId);
        
        if (!$originalAdmin || $originalAdmin->type !== 'super admin') {
            return null;
        }

        // Determine the company ID
        if (!$companyId && $admin) {
            $companyId = $admin->id;
        }

        return AdminActivityLog::logActivity(
            $companyId,
            $originalAdmin->id,
            $action,
            $model,
            $modelId,
            $changes,
            $originalAdmin->creatorId()
        );
    }

    /**
     * Log a login activity
     */
    public static function logLogin($companyId)
    {
        $adminId = session('admin_impersonating_company_id');
        $admin = \App\Models\User::find($adminId);
        
        if (!$admin || $admin->type !== 'super admin') {
            return null;
        }

        return AdminActivityLog::logActivity(
            $companyId,
            $admin->id,
            'login',
            null,
            null,
            null,
            $admin->creatorId()
        );
    }

    /**
     * Log a logout activity
     */
    public static function logLogout($companyId, $adminId)
    {
        return AdminActivityLog::logActivity(
            $companyId,
            $adminId,
            'logout',
            null,
            null,
            null,
            $adminId
        );
    }

    /**
     * Log create action
     */
    public static function logCreate($model, $modelId, $data, $companyId = null)
    {
        return self::log('create', class_basename($model), $modelId, json_encode(['new' => $data]), $companyId);
    }

    /**
     * Log update action
     */
    public static function logUpdate($model, $modelId, $oldData, $newData, $companyId = null)
    {
        $changes = [];
        foreach ($newData as $key => $value) {
            if (($oldData[$key] ?? null) !== $value) {
                $changes[$key] = [
                    'old' => $oldData[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        return self::log('update', class_basename($model), $modelId, json_encode($changes), $companyId);
    }

    /**
     * Log delete action
     */
    public static function logDelete($model, $modelId, $data, $companyId = null)
    {
        return self::log('delete', class_basename($model), $modelId, json_encode(['deleted' => $data]), $companyId);
    }

    /**
     * Log status change
     */
    public static function logStatusChange($model, $modelId, $oldStatus, $newStatus, $companyId = null)
    {
        return self::log('status_change', class_basename($model), $modelId, json_encode([
            'status' => [
                'old' => $oldStatus,
                'new' => $newStatus,
            ]
        ]), $companyId);
    }

    /**
     * Log payment received/recorded
     */
    public static function logPaymentReceived($invoice, $amount, $paymentMethod = null, $companyId = null)
    {
        return self::log('payment_received', 'Invoice', $invoice->id, json_encode([
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'invoice_number' => $invoice->invoice_id ?? $invoice->id,
        ]), $companyId);
    }

    /**
     * Log payment refund
     */
    public static function logPaymentRefund($invoice, $amount, $reason = null, $companyId = null)
    {
        return self::log('payment_refund', 'Invoice', $invoice->id, json_encode([
            'amount' => $amount,
            'reason' => $reason,
            'invoice_number' => $invoice->invoice_id ?? $invoice->id,
        ]), $companyId);
    }

    /**
     * Log invoice sent
     */
    public static function logInvoiceSent($invoice, $recipientEmail = null, $companyId = null)
    {
        return self::log('invoice_sent', 'Invoice', $invoice->id, json_encode([
            'invoice_number' => $invoice->invoice_id ?? $invoice->id,
            'recipient_email' => $recipientEmail ?? $invoice->customer->email ?? 'unknown',
        ]), $companyId);
    }

    /**
     * Log bulk action
     */
    public static function logBulkAction($action, $modelType, $count, $ids = [], $companyId = null)
    {
        return self::log('bulk_' . $action, $modelType, null, json_encode([
            'action' => $action,
            'count' => $count,
            'ids' => $ids,
        ]), $companyId);
    }

    /**
     * Log batch import
     */
    public static function logBatchImport($modelType, $count, $fileName = null, $companyId = null)
    {
        return self::log('bulk_import', $modelType, null, json_encode([
            'count' => $count,
            'file_name' => $fileName,
        ]), $companyId);
    }

    /**
     * Log export action
     */
    public static function logExport($modelType, $count, $format = 'csv', $companyId = null)
    {
        return self::log('export', $modelType, null, json_encode([
            'format' => $format,
            'count' => $count,
        ]), $companyId);
    }

    /**
     * Log settings change
     */
    public static function logSettingsChange($settingName, $oldValue, $newValue, $companyId = null)
    {
        return self::log('settings_change', 'Settings', null, json_encode([
            'setting' => $settingName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ]), $companyId);
    }

    /**
     * Log permission/role change
     */
    public static function logPermissionChange($userId, $action, $roleOrPermission, $companyId = null)
    {
        return self::log('permission_change', 'User', $userId, json_encode([
            'action' => $action,
            'role_or_permission' => $roleOrPermission,
        ]), $companyId);
    }

    /**
     * Check if currently impersonating
     */
    public static function isImpersonating()
    {
        return session()->has('admin_impersonating_company_id');
    }

    /**
     * Get the original admin ID
     */
    public static function getAdminId()
    {
        return session()->get('admin_impersonating_company_id');
    }
}
