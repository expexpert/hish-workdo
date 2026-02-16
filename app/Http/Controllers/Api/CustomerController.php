<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientNotification;
use Illuminate\Http\JsonResponse;
use App\Models\ClientTransaction;
use App\Models\ClientBankStatement;
use Carbon\Carbon;



class CustomerController extends Controller
{

    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Customer profile retrieved successfully.',
            'data'    => $user
        ], 200);
    }


    public function getDashboardData(Request $request): JsonResponse
    {
        $user = $request->user();

        // Set 'this_week' as the default filter if none is provided
        $filter = $request->query('filter', 'this_week');

        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now();

        // Change dates based on selection
        switch ($filter) {
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                break;
            case 'last_year':
                $startDate = Carbon::now()->subYear()->startOfYear();
                $endDate = Carbon::now()->subYear()->endOfYear();
                break;
            case 'all':
                $startDate = null; // No date restriction
                break;
            case 'this_week':
            default:
                $startDate = Carbon::now()->startOfWeek();
                break;
        }

        // 1. Base Query for Transactions
        $transactionQuery = ClientTransaction::where('customer_id', $user->id);

        // 2. Apply Date Filter
        if ($startDate) {
            $transactionQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // 3. Calculate Totals (using clone to reuse the date filters)
        $totalExpenses = (clone $transactionQuery)->where('type', 'expense')->sum('amount');
        $totalRevenue = (clone $transactionQuery)->where('type', 'revenue')->sum('amount');

        // 4. Other data
        $totalProgressData = $user->invoiceChartData()['progressData'];
        $isNotification = ClientNotification::where('customer_id', $user->id)
            ->where('is_read', false)
            ->exists();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully.',
            'applied_filter' => $filter,
            'data' => [
                'total_expenses' => (float) $totalExpenses,
                'total_revenue' => (float) $totalRevenue,
                'has_unread_notifications' => $isNotification,
                'progress_data' => $totalProgressData
            ]
        ], 200);
    }

    public function getCustomerNotifications(Request $request): JsonResponse
    {
        $user = $request->user();

        // Prepare the data payload
        $data = [
            'notifications' => ClientNotification::where('customer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Customer notifications retrieved successfully.',
            'data'    => $data
        ], 200);
    }

    public function viewSingleNotification(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        // Find the notification by ID and ensure it belongs to the authenticated customer
        $notification = ClientNotification::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found or does not belong to the customer.'
            ], 404);
        }

        // Mark the notification as read if it's not already
        if (! $notification->is_read) {
            $notification->is_read = true;
            $notification->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification retrieved successfully.',
            'data'    => $notification
        ], 200);
    }

    public function clearNotifications(Request $request): JsonResponse
    {
        $user = $request->user();

        // Delete all notifications for the authenticated customer
        ClientNotification::where('customer_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared successfully.'
        ], 200);
    }


    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'type'             => 'required|in:expense,revenue',
            'transaction_date' => 'required|date',
            'amount'           => 'required|numeric|min:0',
            'customer_id'       => 'required|exists:customers,id',
            'account_id'       => 'required|exists:bank_accounts,id',
            'category_id'      => 'required|exists:product_service_categories,id',
            'description'      => 'nullable|string',
            'reference'        => 'nullable|string|max:255',
            'payment_receipt'  => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Handle File Upload for "Payment Receipt"
        if ($request->hasFile('payment_receipt')) {
            $path = $request->file('payment_receipt')->store('receipts', 'public');
            $validated['attachment_path'] = $path;
        }

        $transaction = ClientTransaction::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Transaction recorded successfully',
            'data'    => $transaction
        ], 201);
    }


    public function getTransactions(Request $request)
    {
        $user = $request->user();

        $transactions = ClientTransaction::where('customer_id', $user->id)
            ->with(['account:id,holder_name', 'category:id,name'])
            ->orderBy('transaction_date', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ], 200);
    }


    public function viewSingleTransaction(Request $request, $id)
    {
        $user = $request->user();

        $transaction = ClientTransaction::where('id', $id)
            ->where('customer_id', $user->id)
            ->with(['account:id,holder_name', 'category:id,name'])
            ->first();

        if (! $transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found or does not belong to the customer.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $transaction
        ], 200);
    }


    public function storeStatement(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'statement' => 'required|mimes:pdf,csv,xls,xlsx|max:10240',
            'month_year' => 'required|string',
        ]);

        $path = $request->file('statement')->store('bank_statements', 'private');

        $statement = ClientBankStatement::create([
            'customer_id' => $request->customer_id,
            'file_path' => $path,
            'month_year' => $request->month_year,
        ]);

        return response()->json(['message' => 'Statement uploaded successfully', 'data' => $statement], 201);
    }

    public function getBankStatements(Request $request)
    {
        $user = $request->user();
        $statements = ClientBankStatement::where('customer_id', $user->id)->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $statements], 200);
    }


    public function viewSingleBankStatement(Request $request, $id)
    {
        $user = $request->user();
        $statement = ClientBankStatement::where('id', $id)->where('customer_id', $user->id)->first();
        if (! $statement) {
            return response()->json(['message' => 'Statement not found or does not belong to the customer.'], 404);
        }
        return response()->json(['data' => $statement], 200);
    }
}
