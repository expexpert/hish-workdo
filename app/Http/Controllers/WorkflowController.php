<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerMonthStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AdminActivityLogger;


class WorkflowController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $user = Auth::user();
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            $customers = Customer::where('id', $customer->id)
                ->with(['monthStatuses' => function ($q) use ($year) {
                    $q->where('year', $year);
                }])->get();
            $canUpdate = false;
            return view('workflow.index', compact('customers', 'year', 'canUpdate'));
        }

        if ($user && $user->type == 'super admin') {
            abort(403);
        }

        $query = Customer::query();
        $canUpdate = false;

        if ($user->type == 'accountant') {
            // Accountant: Only see customers they created directly
            $query->where('created_by', $user->id);
            $canUpdate = true;
        } elseif ($user->type == 'company') {
            // Company: See customers created by any accountant that THIS company created
            $myAccountantIds = User::where('created_by', $user->id)->pluck('id');
            $query->whereIn('created_by', $myAccountantIds);
            $canUpdate = true;
            $accountantNames = User::whereIn('id', $myAccountantIds)->pluck('name', 'id');
        }
        // Superadmin: No filter, sees all customers from all companies

        $customers = $query->with(['monthStatuses' => function ($q) use ($year) {
            $q->where('year', $year);
        }])->orderBy('name')->get();

        if (isset($accountantNames)) {
            return view('workflow.index', compact('customers', 'year', 'canUpdate', 'accountantNames'));
        }
        return view('workflow.index', compact('customers', 'year', 'canUpdate'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'status' => 'required|in:ON_TRACK,MISSING_DOCUMENTS,IN_REVIEW,CLOSED'
        ]);

        if (Auth::guard('customer')->check()) {
            abort(403);
        }

        $user = Auth::user();
        if ($user && $user->type == 'super admin') {
            abort(403);
        }

        $customer = Customer::findOrFail($request->customer_id);

        if ($user->type == 'accountant') {
            if ($customer->created_by != $user->id) {
                abort(403);
            }
        } elseif ($user->type == 'company') {
            $myAccountantIds = User::where('created_by', $user->id)->pluck('id')->toArray();
            if (!in_array($customer->created_by, $myAccountantIds)) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $existing = CustomerMonthStatus::where('customer_id', $request->customer_id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->first();
        $oldStatus = $existing?->status;

        $status = CustomerMonthStatus::updateOrCreate(
            [
                'customer_id' => $request->customer_id,
                'month'       => $request->month,
                'year'        => $request->year,
            ],
            [
                'status'             => $request->status,
                'updated_by_user_id' => Auth::id(),
            ]
        );

        AdminActivityLogger::logStatusChange(CustomerMonthStatus::class, $status->id, $oldStatus, $request->status);

        return response()->json(['success' => true]);
    }
}
