<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientNotification;
use Illuminate\Http\JsonResponse;
use App\Models\ClientTransaction;
use App\Models\ClientBankStatement;
use App\Models\Customer;
use App\Models\CustomerClient;
use App\Models\CustomerExpense;
use App\Models\CustomerInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;



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

        // 4. Other data points (e.g., unread notifications)
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
                'has_unread_notifications' => $isNotification
            ]
        ], 200);
    }


    public function getAccountantInfo(Request $request): JsonResponse
    {
        $user = $request->user();

        $accountant = $user->accountant;

        if (! $accountant) {
            return response()->json([
                'success' => false,
                'message' => 'No accountant information found for this customer.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Accountant information retrieved successfully.',
            'data'    => $accountant
        ], 200);
    }


    public function getCustomerNotifications(Request $request): JsonResponse
    {
        $user = $request->user();

        // Prepare the data payload
        $data = [
            'notifications' => ClientNotification::where('customer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->where('data', 'notification')
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
        ClientNotification::where('customer_id', $user->id)->where('data', 'notification')->delete();

        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared successfully.'
        ], 200);
    }

    public function getDocuments(Request $request): JsonResponse
    {
        $user = $request->user();
        $documentType = $request->get('documentType', 'juridiques');

        // Prepare the data payload
        $data = [
            'documents' => ClientNotification::where('customer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->where('data', 'document_notification')
                ->where('title', $documentType)
                ->limit(20)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Customer documents retrieved successfully.',
            'data'    => $data
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


    public function getWorkflowStatus(Request $request)
    {
        $user = $request->user();
        $year = $request->get('year', date('Y'));

        $customers = Customer::where('id', $user->id)
            ->with(['monthStatuses' => function ($q) use ($year) {
                $q->where('year', $year);
            }])->get();

        return response()->json([
            'success' => true,
            'message' => 'Workflow status retrieved successfully.',
            'data'    => [
                'customers' => $customers,
                'year' => $year
            ]
        ], 200);
    }


    public function storeCustomerClient(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customer_clients,email',
            'telephone' => 'nullable|string|max:20',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'commercial_register' => 'nullable|string|max:255',
            'ice' => 'nullable|string|max:255',
        ]);

        $validated['customer_id'] = $request->user()->id;

        $client = CustomerClient::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer client created successfully.',
            'data'    => $client
        ], 201);
    }

    public function getCustomerClients(Request $request)
    {
        $user = $request->user();
        $like = $request->query('like');
        $clients = CustomerClient::where('customer_id', $user->id);
        if ($like) {
            $clients = $clients->where('company_name', 'like', "%$like%")->OrWhere('client_name', 'like', "%$like%");
        }
        $clients = $clients->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Customer clients retrieved successfully.',
            'data'    => $clients
        ], 200);
    }

    public function viewSingleCustomerClient(Request $request, $id)
    {
        $user = $request->user();
        $client = CustomerClient::where('id', $id)->where('customer_id', $user->id)->first();

        if (! $client) {
            return response()->json([
                'success' => false,
                'message' => 'Customer client not found or does not belong to the customer.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer client retrieved successfully.',
            'data'    => $client
        ], 200);
    }

    public function updateCustomerClient(Request $request, $id)
    {
        $user = $request->user();
        $client = CustomerClient::where('id', $id)->where('customer_id', $user->id)->first();

        if (! $client) {
            return response()->json([
                'success' => false,
                'message' => 'Customer client not found or does not belong to the customer.'
            ], 404);
        }

        $validated = $request->validate([
            'company_name' => 'sometimes|required|string|max:255',
            'client_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customer_clients,email,' . $client->id,
            'telephone' => 'nullable|string|max:20',
            'postal_code' => 'sometimes|required|string|max:20',
            'city' => 'sometimes|required|string|max:100',
            'commercial_register' => 'nullable|string|max:255',
            'ice' => 'nullable|string|max:255',
        ]);

        $client->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer client updated successfully.',
            'data'    => $client
        ], 200);
    }

    public function deleteCustomerClient(Request $request, $id)
    {
        $user = $request->user();
        $client = CustomerClient::where('id', $id)->where('customer_id', $user->id)->first();

        if (! $client) {
            return response()->json([
                'success' => false,
                'message' => 'Customer client not found or does not belong to the customer.'
            ], 404);
        }

        $client->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer client deleted successfully.'
        ], 200);
    }


    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'file'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'date'           => 'required|date',
            'ttc'            => 'required|numeric|min:0',
            'tva'            => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'category_id'    => 'required|exists:product_service_categories,id',
            'total_ttc'      => 'nullable|numeric|min:0',
            'total_tva'      => 'nullable|numeric|min:0',
        ]);

        // Handle File Upload
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('expenses', 'public');
            $validated['file'] = $path;
        }

        $expense = CustomerExpense::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expense recorded successfully.',
            'debug_info' => [
                'received_payload' => $request->all(),
                'server_time' => now()->toDateTimeString()
            ],
            'data' => $expense
        ], 201);
    }


    public function getExpenses(Request $request)
    {
        $user = $request->user();

        $expenses = CustomerExpense::where('customer_id', $user->id)
            ->with('category:id,name')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Customer expenses retrieved successfully.',
            'data'    => $expenses
        ], 200);
    }


    public function viewSingleExpense(Request $request, $id)
    {
        $user = $request->user();

        $expense = CustomerExpense::where('id', $id)
            ->where('customer_id', $user->id)
            ->with('category:id,name')
            ->first();

        if (! $expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found or does not belong to the customer.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Expense retrieved successfully.',
            'data'    => $expense
        ], 200);
    }


    public function updateExpense(Request $request, $id)
    {
        $user = $request->user();

        $expense = CustomerExpense::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (! $expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found or does not belong to the customer.'
            ], 404);
        }

        $validated = $request->validate([
            'file'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'date'           => 'sometimes|required|date',
            'ttc'            => 'sometimes|required|numeric|min:0',
            'tva'            => 'nullable|numeric|min:0',
            'payment_method' => 'sometimes|required|string|max:255',
            'category_id'    => 'sometimes|required|exists:product_service_categories,id',
            'total_ttc'      => 'nullable|numeric|min:0',
            'total_tva'      => 'nullable|numeric|min:0',
        ]);

        // Handle File Upload
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('expenses', 'public');
            $validated['file'] = $path;
        }

        $expense->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully.',
            'data'    => $expense
        ], 200);
    }


    public function deleteExpense(Request $request, $id)
    {
        $user = $request->user();

        $expense = CustomerExpense::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (! $expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found or does not belong to the customer.'
            ], 404);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully.'
        ], 200);
    }



    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            // Invoice Header
            'customer_id'    => 'required|exists:customers,id',
            'client_id'      => 'required|exists:customer_clients,id',
            'date'           => 'required|date',
            'invoice_number' => 'required|string|max:255|unique:customer_invoices,invoice_number',
            'payment_method' => 'required|string|max:255',
            'status'         => 'required|string|max:50',
            'document'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',


            'articles'                 => 'sometimes|array',
            'articles.*.designation'    => 'required_with:articles|string|max:255',
            'articles.*.unit_price_ht' => 'required_with:articles|numeric|min:0',
            'articles.*.quantity'      => 'required_with:articles|integer|min:1',
            'articles.*.total_price_ht' => 'required_with:articles|numeric|min:0',
            'articles.*.tva_percentage' => 'required_with:articles|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // 1. Handle File Upload
                if ($request->hasFile('document')) {
                    $path = $request->file('document')->store('customer_invoices', 'public');
                    $validated['document_path'] = $path;
                }

                // 2. Create the Invoice Header
                $invoice = CustomerInvoice::create($validated);

                // 3. Create Articles ONLY if they exist in the request
                if (!empty($validated['articles'])) {
                    $invoice->articles()->createMany($validated['articles']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice created successfully.',
                    'data'    => $invoice->load('articles')
                ], 201);
            });
        } catch (\Exception $e) {
            if (isset($validated['document_path'])) {
                Storage::disk('public')->delete($validated['document_path']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getInvoices(Request $request)
    {
        $user = $request->user();

        $invoices = CustomerInvoice::where('customer_id', $user->id)
            ->with(['client:id,client_name', 'articles'])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Customer invoices retrieved successfully.',
            'data'    => $invoices
        ], 200);
    }


    public function viewSingleInvoice(Request $request, $id)
    {
        $user = $request->user();

        $invoice = CustomerInvoice::where('id', $id)
            ->where('customer_id', $user->id)
            ->with(['client:id,client_name', 'articles'])
            ->first();

        if (! $invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found or does not belong to the customer.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice retrieved successfully.',
            'data'    => $invoice
        ], 200);
    }


    public function updateInvoice(Request $request, $id)
    {
        $user = $request->user();

        $invoice = CustomerInvoice::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found or does not belong to the customer.'
            ], 404);
        }

        $validated = $request->validate([
            'client_id'      => 'sometimes|required|exists:customer_clients,id',
            'date'           => 'sometimes|required|date',
            'invoice_number' => 'sometimes|required|string|max:255|unique:customer_invoices,invoice_number,' . $invoice->id,
            'payment_method' => 'sometimes|required|string|max:255',
            'status'         => 'sometimes|required|string|max:50',
            'document'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',

            // Articles validation (optional during update)
            'articles'                 => 'sometimes|array',
            'articles.*.designation'    => 'required_with:articles|string|max:255',
            'articles.*.unit_price_ht' => 'required_with:articles|numeric|min:0',
            'articles.*.quantity'      => 'required_with:articles|integer|min:1',
            'articles.*.total_price_ht' => 'required_with:articles|numeric|min:0',
            'articles.*.tva_percentage' => 'required_with:articles|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated, $invoice) {

                // 1. Handle File Upload (and delete old file if a new one is uploaded)
                if ($request->hasFile('document')) {
                    if ($invoice->document_path) {
                        Storage::disk('public')->delete($invoice->document_path);
                    }
                    $path = $request->file('document')->store('customer_invoices', 'public');
                    $validated['document_path'] = $path;
                }

                // 2. Update Invoice Header
                $invoice->update($validated);

                if ($request->has('articles')) {
                    // Delete existing articles first
                    $invoice->articles()->delete();

                    // If the array isn't empty, create the new ones
                    if (!empty($validated['articles'])) {
                        $invoice->articles()->createMany($validated['articles']);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice and articles updated successfully.',
                    'data'    => $invoice->load('articles')
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteInvoice(Request $request, $id)
    {
        $user = $request->user();

        $invoice = CustomerInvoice::where('id', $id)
            ->where('customer_id', $user->id)
            ->first(); 

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found or does not belong to the customer.'
            ], 404);
        }

        try {
            return DB::transaction(function () use ($invoice) {
                // 1. Delete the physical file from storage if it exists
                if ($invoice->document_path) {
                    Storage::disk('public')->delete($invoice->document_path);
                }

                // Delete associated articles first
                $invoice->articles()->delete();                
                $invoice->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice and associated files deleted successfully.'
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice: ' . $e->getMessage()
            ], 500);
        }
    }
}
