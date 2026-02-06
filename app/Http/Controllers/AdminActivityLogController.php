<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    /**
     * Display all activity logs for the company
     */
    public function index()
    {
        $user = \Auth::user();

        // Only company accounts can view activity logs
        if ($user->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        // Get all activity logs for this company
        $logs = AdminActivityLog::where('company_id', $user->id)
            ->with(['admin', 'company'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin-activity-logs.index', compact('logs'));
    }

    /**
     * Show details of a specific activity log
     */
    public function show($id)
    {
        $user = \Auth::user();
        $log = AdminActivityLog::findOrFail($id);

        // Verify the user owns this log
        if ($log->company_id !== $user->id) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $log->load(['admin', 'company']);

        return view('admin-activity-logs.show', compact('log'));
    }

    /**
     * Delete an activity log
     */
    public function destroy($id)
    {
        $user = \Auth::user();
        $log = AdminActivityLog::findOrFail($id);
    
        if ($log->company_id !== $user->id) {
            if (request()->ajax()) {
                return response()->json(['error' => __('Permission denied.')], 403);
            }
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    
        $log->delete();
    
        // Check if the request is AJAX
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Activity log deleted successfully.')
            ]);
        }
    
        return redirect()->back()->with('success', __('Activity log deleted successfully.'));
    }

    /**
     * Export activity logs to CSV
     */
    public function export()
    {
        $user = \Auth::user();

        if ($user->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $logs = AdminActivityLog::where('company_id', $user->id)
            ->with(['admin'])
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=admin_activity_logs.csv',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Date',
                'Admin',
                'Action',
                'Model',
                'Model ID',
                'IP Address',
                'Details'
            ]);

            // Data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->admin ? $log->admin->name . ' (' . $log->admin->email . ')' : 'Unknown',
                    ucfirst($log->action),
                    $log->model,
                    $log->model_id,
                    $log->ip_address,
                    $log->changes ? json_encode($log->changes) : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
