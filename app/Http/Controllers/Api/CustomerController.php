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
use Illuminate\Support\Facades\Mail;
use App\Models\Utility;
use App\Models\InvoiceArticle;
use Illuminate\Validation\ValidationException;
use App\Models\CustomerProduct;
use App\Models\CustomerSupplier;
use App\Models\CustomerMonthStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;

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


    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            // If these keys are present in the request, they MUST have a value (cannot be empty)
            'name'             => 'sometimes|required|string|max:255',
            'email'            => 'sometimes|required|email|unique:customers,email,' . $user->id,
            'bio'              => 'sometimes|required|string|max:1000',
            'short_bio'        => 'sometimes|required|string|max:255',
            'ice_number'       => 'sometimes|required|string|max:255',
            'rc_number'        => 'sometimes|required|string|max:255',
            'patent_number'    => 'sometimes|required|string|max:255',
            'if_number'        => 'sometimes|required|string|max:255',
            'cnss'             => 'sometimes|required|string|max:255',
            'company_type'     => 'sometimes|required|string|max:255',
            'contact'          => 'sometimes|required|string|max:20',
            'address'          => 'sometimes|required|string|max:255',
            'billing_name'     => 'sometimes|required|string|max:255',
            'billing_phone'    => 'sometimes|required|string|max:20',
            'vat_number'       => 'sometimes|required|string|max:255',
            'billing_address'  => 'sometimes|required|string|max:255',
            'billing_zip'      => 'sometimes|required|string|max:20',
            'billing_city'     => 'sometimes|required|string|max:100',

            // These can be present and empty (null), or missing entirely
            'website'          => 'nullable|string|max:255',
            'avatar'           => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'signature'        => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        if ($request->hasFile('signature')) {
            if ($user->signature) {
                Storage::disk('public')->delete($user->signature);
            }
            $path = $request->file('signature')->store('signatures', 'public');
            $validated['signature'] = $path;
        }
        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer profile updated successfully.',
            'data'    => $user
        ], 200);
    }

    public function deleteProfile(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $customer = Customer::where('user_id', $userId)->first();

        if ($customer) {
            $customer->delete();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Customer record not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer profile deleted successfully.'
        ], 200);
    }


    public function getDashboardData(Request $request): JsonResponse
    {
        $user = $request->user();

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // Combined Invoice Stats - only one query for all sums
        $invoiceStats = CustomerInvoice::join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
            ->where('customer_invoices.customer_id', $user->id)
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('customer_invoices.date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('customer_invoices.date', '<=', $dateTo);
            })
            ->select(
                DB::raw("SUM(CASE WHEN customer_invoices.status IN ('ISSUED', 'PAID') THEN invoice_articles.total_price_ht ELSE 0 END) as total_issued_paid_sum"),
                DB::raw("SUM(CASE WHEN customer_invoices.status = 'PAID' THEN invoice_articles.total_price_ht ELSE 0 END) as total_paid_sum"),
                DB::raw("SUM(CASE WHEN customer_invoices.status = 'ISSUED' THEN invoice_articles.total_price_ht ELSE 0 END) as total_issued_sum"),
                DB::raw("SUM(CASE WHEN customer_invoices.status = 'QUOTE' THEN invoice_articles.total_price_ht ELSE 0 END) as total_quote_sum"),
                DB::raw("SUM(CASE WHEN customer_invoices.status IN ('PAID') THEN invoice_articles.tva_percentage ELSE 0 END) as vat_collected"),

                DB::raw("COUNT(DISTINCT CASE WHEN customer_invoices.status = 'ISSUED' THEN customer_invoices.id END) as total_issued_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN customer_invoices.status = 'QUOTE' THEN customer_invoices.id END) as total_quote_count")
            )
            ->first();

        // Combined Expense Stats - only one query for both sums
        $expenseStats = CustomerExpense::where('customer_id', $user->id)
            ->when($dateFrom, function ($query, $dateFrom) {
                $query->whereDate('date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                $query->whereDate('date', '<=', $dateTo);
            })
            ->select(
                DB::raw("SUM(total_ttc) as total_sum"),
                DB::raw("SUM(total_tva) as total_tva")
            )
            ->first();

        $totalVatPayable = ($invoiceStats->vat_collected ?? 0) - ($expenseStats->total_tva ?? 0);

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully.',
            'data'    => [
                'total_issued_paid_sum' => (float) ($invoiceStats->total_issued_paid_sum ?? 0),
                'total_paid_sum' => (float) ($invoiceStats->total_paid_sum ?? 0),
                'total_expenses_sum' => (float) ($expenseStats->total_sum ?? 0),
                'total_vat_payable' => (float) $totalVatPayable,
                'total_issued_count' => $invoiceStats->total_issued_count,
                'total_quote_count' => $invoiceStats->total_quote_count,
                'total_issued_sum' => (float) ($expenseStats->total_issued_sum ?? 0),
                'total_quote_sum' => (float) ($expenseStats->total_quote_sum ?? 0),
            ]
        ], 200);
    }


    public function getDashboardGraphData(Request $request)
    {
        $user = $request->user();
        $year = $request->get('year', date('Y'));

        // Use whereBetween for better performance
        $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endDate = $startDate->copy()->endOfYear();

        // 1. Fetch Invoices (CA) grouped by month
        $invoices = CustomerInvoice::join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
            ->where('customer_invoices.customer_id', $user->id)
            ->whereIn('customer_invoices.status', ['ISSUED', 'PAID'])
            ->whereBetween('customer_invoices.date', [$startDate, $endDate])
            ->select(
                DB::raw('MONTH(customer_invoices.date) as month'),
                DB::raw('SUM(invoice_articles.total_price_ht) as total')
            )
            ->groupBy('month')
            ->pluck('total', 'month');

        // 2. Fetch Expenses grouped by month
        $expenses = CustomerExpense::where('customer_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(ttc) as total')
            )
            ->groupBy('month')
            ->pluck('total', 'month');

        // 3. Build the formatted arrays
        $caFormatted = [];
        $expensesFormatted = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthLabel = Carbon::create()->month($m)->format('M');

            $caFormatted[] = [
                'label' => $monthLabel,
                'value' => (float)($invoices->get($m, 0))
            ];

            $expensesFormatted[] = [
                'label' => $monthLabel,
                'value' => (float)($expenses->get($m, 0))
            ];
        }

        return response()->json([
            'year' => $year,
            'chart' => [
                'ca' => $caFormatted,
                'expenses' => $expensesFormatted
            ]
        ]);
    }


    public function hasUnreadNotifications(Request $request): JsonResponse
    {
        $user = $request->user();

        // Optimized: Check is_read first as it's a simple boolean and likely indexed
        $hasUnread = ClientNotification::where('customer_id', $user->id)
            ->where('is_read', false)
            ->where('data', 'like', '%"notification"%')
            ->exists();

        return response()->json([
            'success' => true,
            'message' => 'Unread notifications status retrieved successfully.',
            'data'    => [
                'has_unread_notifications' => $hasUnread
            ]
        ], 200);
    }


    public function getAccountantInfo(Request $request): JsonResponse
    {
        $user = $request->user();

        // Use caching for accountant info as it rarely changes
        $accountant = Cache::remember("customer_accountant_{$user->id}", 3600, function () use ($user) {
            return $user->accountant;
        });

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

        // Prepare the data payload - optimize by checking is_read first
        $data = [
            'notifications' => ClientNotification::where('customer_id', $user->id)
                ->where('is_read', false)
                ->where('data', 'like', '%"notification"%')
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
        ClientNotification::where('customer_id', $user->id)->where('data', 'like', '%"notification"%')->delete();

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
                ->where('data', 'like', '%"document_notification"%')
                ->where('title', $documentType)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Customer documents retrieved successfully.',
            'data'    => $data
        ], 200);
    }

    public function downloadDocument($id, Request $request)
    {
        $notification = ClientNotification::where('customer_id', $request->user()->id)
            ->findOrFail($id);

        if (!$notification->document || !Storage::disk('public')->exists($notification->document)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        // This forces a download response
        return Storage::disk('public')->download($notification->document, 'Document_' . $notification->id . '.' . pathinfo($notification->document, PATHINFO_EXTENSION));
    }


    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'type'             => 'required|in:expense,revenue',
            'transaction_date' => 'required|date',
            'amount'           => 'required|numeric|min:0',
            'customer_id'       => 'required|exists:customers,id',
            'account_id'       => 'required|exists:bank_accounts,id',
            'category_id'      => 'required|exists:customer_categories,id',
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

    public function downloadReceipt($id, Request $request)
    {
        $transaction = ClientTransaction::where('customer_id', $request->user()->id)
            ->findOrFail($id);

        if (!$transaction->attachment_path || !Storage::disk('public')->exists($transaction->attachment_path)) {
            return response()->json(['status' => 'error', 'message' => 'Receipt not found.'], 404);
        }

        return Storage::disk('public')->download($transaction->attachment_path, 'Receipt_' . $transaction->id . '.' . pathinfo($transaction->attachment_path, PATHINFO_EXTENSION));
    }


    public function storeStatement(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'statement' => 'required|mimes:pdf,csv,xls,xlsx,jpg,jpeg,png|max:10240',
            'month_year' => 'required|string',
        ]);

        $path = $request->file('statement')->store('bank_statements', 'private');

        $statement = ClientBankStatement::updateOrCreate(
            [
                'customer_id' => $request->customer_id,
                'month_year'  => $request->month_year,
            ],
            [
                'file_path'   => $path,
            ]
        );

        $date = Carbon::createFromFormat('m-Y', $request->month_year);

        $statusRecord = CustomerMonthStatus::where([
            'customer_id' => $request->customer_id,
            'month'       => $date->month,
            'year'        => $date->year,
        ])->first();

        if ($statusRecord) {
            $statusRecord->delete();
        }

        $status = $statement->wasRecentlyCreated ? 201 : 200;
        $message = $statement->wasRecentlyCreated ? 'Statement uploaded successfully' : 'Statement updated successfully';

        return response()->json(['message' => $message, 'data' => $statement], $status);
    }


    public function getBankStatements(Request $request)
    {
        $user = $request->user();

        // 1. Get the filter input (e.g., '2025' or '6')
        $filter = $request->query('filter');

        $query = ClientBankStatement::select('client_bank_statement.*', 'customer_month_statuses.status')
            ->leftJoin('customer_month_statuses', function ($join) {
                $join->on('customer_month_statuses.customer_id', '=', 'client_bank_statement.customer_id')
                    ->whereRaw("customer_month_statuses.month = MONTH(STR_TO_DATE(client_bank_statement.month_year, '%m-%Y'))")
                    ->whereRaw("customer_month_statuses.year = YEAR(STR_TO_DATE(client_bank_statement.month_year, '%m-%Y'))");
            })
            ->where('client_bank_statement.customer_id', $user->id);

        if (is_numeric($filter)) {
            if (strlen($filter) === 4) {
                // Filter by year e.g. '2026'
                $query->whereRaw("YEAR(STR_TO_DATE(client_bank_statement.month_year, '%m-%Y')) = ?", [$filter]);
            } else {
                // Filter by duration in months from start of current year e.g. '3', '6'
                $months = (int) $filter;
                $query->whereRaw("YEAR(STR_TO_DATE(client_bank_statement.month_year, '%m-%Y')) = ?", [date('Y')])
                    ->whereRaw("MONTH(STR_TO_DATE(client_bank_statement.month_year, '%m-%Y')) BETWEEN 1 AND ?", [$months]);
            }
        } else {
            // Default: first 3 months of current year
            $query->whereRaw("YEAR(STR_TO_DATE(client_bank_statement.month_year, '%m-%Y')) = ?", [date('Y')])
                ->whereRaw("MONTH(STR_TO_DATE(client_bank_statement.month_year, '%m-%Y')) BETWEEN 1 AND 3");
        }

        $statements = $query->orderByRaw("STR_TO_DATE(client_bank_statement.month_year, '%m-%Y') DESC")->get();

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


    public function downloadBankStatement($id, Request $request)
    {
        $document = ClientBankStatement::where('customer_id', $request->user()->id)
            ->findOrFail($id);

        $filePath = $document->file_path;

        if (!$filePath || !Storage::disk('private')->exists($filePath)) {
            return response()->json(['message' => 'Statement file not found.'], 404);
        }


        return Storage::disk('private')->download($filePath, 'BankStatement_' . $document->month_year . '_' . $document->id . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
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

        // Get client + invoice count in same query
        $client = CustomerClient::where('id', $id)
            ->where('customer_id', $user->id)
            ->withCount('invoices')
            ->first();

        if (! $client) {
            return response()->json([
                'success' => false,
                'message' => 'Customer client not found or does not belong to the customer.'
            ], 404);
        }

        $totalPriceHt = $client->invoices()
            ->join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
            ->sum('invoice_articles.total_price_ht');

        return response()->json([
            'success' => true,
            'message' => 'Customer client retrieved successfully.',
            'data' => [
                'client'         => $client,
                'invoice_count'  => $client->invoices_count,
                'total_price_ht' => (float) $totalPriceHt,
            ]
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


    public function getCustomerClientInvoices(Request $request, $id)
    {
        $user = $request->user();

        $client = CustomerClient::withTrashed()->where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (! $client) {
            return response()->json([
                'success' => false,
                'message' => 'Customer client not found or does not belong to the customer.'
            ], 404);
        }

        $invoices = CustomerInvoice::where('client_id', $client->id)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Invoices for the customer client retrieved successfully.',
            'data'    => $invoices
        ], 200);
    }


    public function storeCustomerSupplier(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customer_suppliers,email',
            'telephone' => 'nullable|string|max:20',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'commercial_register' => 'nullable|string|max:255',
            'ice' => 'nullable|string|max:255',
        ]);

        $validated['customer_id'] = $request->user()->id;

        $supplier = CustomerSupplier::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer supplier created successfully.',
            'data'    => $supplier
        ], 201);
    }


    public function getCustomerSuppliers(Request $request)
    {
        $user = $request->user();
        $like = $request->query('like');
        $suppliers = CustomerSupplier::where('customer_id', $user->id);
        if ($like) {
            $suppliers = $suppliers->where('company_name', 'like', "%$like%")->OrWhere('supplier_name', 'like', "%$like%");
        }
        $suppliers = $suppliers->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Customer suppliers retrieved successfully.',
            'data'    => $suppliers
        ], 200);
    }

    public function viewSingleCustomerSupplier(Request $request, $id)
    {
        $user = $request->user();

        // Get supplier + invoice count in same query
        $supplier = CustomerSupplier::where('id', $id)
            ->where('customer_id', $user->id)
            ->withCount('expenses')
            ->first();

        if (! $supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Customer supplier not found or does not belong to the customer.'
            ], 404);
        }

        $totalPricettc = $supplier->expenses()
            ->sum('ttc');

        return response()->json([
            'success' => true,
            'message' => 'Customer supplier retrieved successfully.',
            'data' => [
                'supplier'       => $supplier,
                'expenses_count'  => $supplier->expenses_count,
                'total_price_ttc' => (float) $totalPricettc,
            ]
        ], 200);
    }


    public function updateCustomerSupplier(Request $request, $id)
    {
        $user = $request->user();
        $supplier = CustomerSupplier::where('id', $id)->where('customer_id', $user->id)->first();

        if (! $supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Customer supplier not found or does not belong to the customer.'
            ], 404);
        }

        $validated = $request->validate([
            'company_name' => 'sometimes|required|string|max:255',
            'supplier_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:customer_suppliers,email,' . $supplier->id,
            'telephone' => 'nullable|string|max:20',
            'postal_code' => 'sometimes|required|string|max:20',
            'city' => 'sometimes|required|string|max:100',
            'commercial_register' => 'nullable|string|max:255',
            'ice' => 'nullable|string|max:255',
        ]);

        $supplier->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer supplier updated successfully.',
            'data'    => $supplier
        ], 200);
    }


    public function deleteCustomerSupplier(Request $request, $id)
    {
        $user = $request->user();
        $supplier = CustomerSupplier::where('id', $id)->where('customer_id', $user->id)->first();

        if (! $supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Customer supplier not found or does not belong to the customer.'
            ], 404);
        }

        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer supplier deleted successfully.'
        ], 200);
    }


    public function getCustomerSupplierExpenses(Request $request, $id)
    {
        $user = $request->user();

        $supplier = CustomerSupplier::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (! $supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Customer supplier not found or does not belong to the customer.'
            ], 404);
        }

        $expenses = CustomerExpense::where('supplier_id', $supplier->id)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Expenses for the customer supplier retrieved successfully.',
            'data'    => $expenses
        ], 200);
    }

    public function storeExpense(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id'    => 'required|exists:customers,id',
                'supplier_id'    => 'required|exists:customer_suppliers,id',
                'file'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
                'date'           => 'required|date',
                'ttc'            => 'required|numeric|min:0',
                'tva'            => 'nullable|numeric|min:0',
                'payment_method' => 'required|string|max:255',
                'category_id'    => 'required|exists:customer_categories,id',
                'total_ttc'      => 'nullable|numeric|min:0',
                'total_tva'      => 'nullable|numeric|min:0',
                'notes'          => 'nullable|string',
            ]);

            // Handle File Upload
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('expenses', 'private');
                $validated['file'] = $path;
            }

            $expense = CustomerExpense::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Expense recorded successfully.',
                'data' => $expense
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
                'debug_info' => [
                    'received_payload' => $request->all(),
                    'server_time' => now()->toDateTimeString()
                ]
            ], 422);
        }
    }


    public function getExpenses(Request $request)
    {
        $user = $request->user();
        $month = $request->query('month');
        $year = $request->query('year');

        $query = CustomerExpense::where('customer_id', $user->id)
            ->with('category:id,name')
            ->orderBy('date', 'desc');

        // Use whereBetween for better performance when year is provided
        if ($year && $month) {
            $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $query->whereBetween('date', [$start, $end]);
        } elseif ($year) {
            $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $end = $start->copy()->endOfYear();
            $query->whereBetween('date', [$start, $end]);
        } elseif ($month) {
            $query->whereMonth('date', $month);
        }

        $expenses = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Customer expenses retrieved successfully.',
            'data'    => $expenses
        ], 200);
    }


    public function getExpenseCategoryChart(Request $request)
    {
        $user = $request->user();
        $month = $request->query('month');
        $year = $request->query('year');

        $query = CustomerExpense::where('customer_id', $user->id)
            ->join('customer_categories', 'customer_expenses.category_id', '=', 'customer_categories.id')
            ->select(
                'customer_expenses.category_id',
                DB::raw('customer_categories.name as label'),
                DB::raw('SUM(COALESCE(customer_expenses.total_ttc, customer_expenses.ttc)) as value')
            );

        // Use whereBetween for better performance when year is provided
        if ($year && $month) {
            $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $query->whereBetween('customer_expenses.date', [$start, $end]);
        } elseif ($year) {
            $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $end = $start->copy()->endOfYear();
            $query->whereBetween('customer_expenses.date', [$start, $end]);
        } elseif ($month) {
            $query->whereMonth('customer_expenses.date', $month);
        }

        $rows = $query->groupBy('customer_expenses.category_id', 'customer_categories.name')
            ->orderByDesc('value')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Expense totals by category retrieved successfully.',
            'data' => $rows
        ], 200);
    }


    public function downloadExpenseFile($id, Request $request)
    {
        $expense = CustomerExpense::where('customer_id', $request->user()->id)
            ->findOrFail($id);

        if (!$expense->file || !Storage::disk('private')->exists($expense->file)) {
            return response()->json([
                'success' => false,
                'message' => 'Expense file not found.'
            ], 404);
        }

        return Storage::disk('private')->download($expense->file, 'Expense_' . $expense->id . '_' . now()->format('Ymd_His') . '.' . pathinfo($expense->file, PATHINFO_EXTENSION));
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
            'supplier_id'    => 'sometimes|required|exists:customer_suppliers,id',
            'file'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'date'           => 'sometimes|required|date',
            'ttc'            => 'sometimes|required|numeric|min:0',
            'tva'            => 'nullable|numeric|min:0',
            'payment_method' => 'sometimes|required|string|max:255',
            'category_id'    => 'sometimes|required|exists:customer_categories,id',
            'total_ttc'      => 'nullable|numeric|min:0',
            'total_tva'      => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        // Handle File Upload
        if ($request->hasFile('file')) {
            if ($expense->file) {
                Storage::disk('private')->delete($expense->file);
            }
            $path = $request->file('file')->store('expenses', 'private');
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

        if ($expense->file) {
            Storage::disk('private')->delete($expense->file);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully.'
        ], 200);
    }

    public function exportExpenses(Request $request)
    {
        $user = $request->user();
        $expenses = CustomerExpense::where('customer_id', $user->id)
            ->with('category:id,name')
            ->orderBy('date', 'desc')
            ->get();

        $fileName = "expenses_" . now()->format('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($expenses) {
            // Open the output stream
            $file = fopen('php://output', 'w');

            // Add CSV Headers
            fputcsv($file, ['Date', 'Amount TTC', 'TVA', 'Payment Method', 'Category', 'Total TTC', 'Total TVA']);

            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->date,
                    $expense->ttc,
                    $expense->tva,
                    $expense->payment_method,
                    $expense->category->name ?? 'N/A',
                    $expense->total_ttc,
                    $expense->total_tva
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            'notes'          => 'nullable|string',
            'document'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',


            'articles'                 => 'sometimes|array',
            'articles.*.designation'    => 'required_with:articles|string|max:255',
            'articles.*.unit_price_ht' => 'required_with:articles|numeric|min:0',
            'articles.*.quantity'        => 'nullable|integer|min:1',
            'articles.*.total_price_ht'  => 'nullable|numeric|min:0',
            'articles.*.tva_percentage' => 'required_with:articles|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // 1. Handle File Upload
                if ($request->hasFile('document')) {
                    $path = $request->file('document')->store('customer_invoices', 'private');
                    $validated['document_path'] = $path;
                }

                // 2. Create the Invoice Header
                $invoice = CustomerInvoice::create($validated);

                // 3. Create Articles ONLY if they exist in the request
                if (!empty($validated['articles'])) {
                    $invoice->articles()->createMany($validated['articles']);

                    foreach ($validated['articles'] as $article) {
                        // Check if product already exists for this customer based on designation
                        CustomerProduct::firstOrCreate(
                            [
                                'customer_id' => $validated['customer_id'],
                                'designation' => $article['designation'],
                            ],
                            [
                                'unit_price_ht'  => $article['unit_price_ht'],
                                'tva_percent'    => $article['tva_percentage'], // Note the name mapping
                                'quantity'       => $article['quantity'],
                                'total_price_ht' => $article['total_price_ht'],
                            ]
                        );
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice created successfully.',
                    'data'    => $invoice->load('articles')
                ], 201);
            });
        } catch (\Exception $e) {
            if (isset($validated['document_path'])) {
                Storage::disk('private')->delete($validated['document_path']);
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

        $month = $request->query('month');
        $year = $request->query('year');
        $status = $request->query('status');

        $query = CustomerInvoice::where('customer_id', $user->id)
            ->with(['client:id,client_name', 'articles'])
            ->orderBy('date', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        // Use whereBetween for better performance when year is provided (avoids YEAR() and MONTH() function calls on indexed column)
        if ($year && $month) {
            $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $query->whereBetween('date', [$start, $end]);
        } elseif ($year) {
            $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $end = $start->copy()->endOfYear();
            $query->whereBetween('date', [$start, $end]);
        } elseif ($month) {
            $query->whereMonth('date', $month);
        }

        $invoices = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Customer invoices retrieved successfully.',
            'data'    => $invoices
        ], 200);
    }


    public function downloadInvoice($id, Request $request)
    {
        $invoice = CustomerInvoice::where('customer_id', $request->user()->id)
            ->findOrFail($id);

        if (!$invoice->document_path || !Storage::disk('private')->exists($invoice->document_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice file not found on server.'
            ], 404);
        }

        return Storage::disk('private')->download($invoice->document_path, 'Invoice_' . $invoice->id . '.' . pathinfo($invoice->document_path, PATHINFO_EXTENSION));
    }

    public function downloadInvoicePdf($id, Request $request)
    {
        $invoice = CustomerInvoice::where('customer_id', $request->user()->id)
            ->with(['client', 'articles', 'customer'])
            ->findOrFail($id);

        $company = $invoice->customer;

        $totals = [
            'total_ht' => $invoice->articles->sum('total_price_ht'),
            'total_tva' => $invoice->articles->sum(function ($a) {
                return round($a->total_price_ht * ($a->tva_percentage / 100), 2);
            }),
        ];
        $totals['total_ttc'] = round($totals['total_ht'] + $totals['total_tva'], 2);
        $totals['average_tva_percentage'] = $totals['total_ht'] > 0 ? round(($totals['total_tva'] / $totals['total_ht']) * 100, 2) : 0;

        $logoUrl = ($company && $company->avatar) ? asset('storage/' . $company->avatar) : null;
        $signatureUrl = ($company && $company->signature) ? asset('storage/' . $company->signature) : null;

        $logoDataUri = null;
        $signatureDataUri = null;

        try {
            if ($company && $company->avatar) {
                $logoPath = storage_path('app/public/' . $company->avatar);
                if (is_file($logoPath)) {
                    $mime = mime_content_type($logoPath) ?: 'image/png';
                    $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
                }
            }
        } catch (\Throwable $e) {
        }

        try {
            if ($company && $company->signature) {
                $sigPath = storage_path('app/public/' . $company->signature);
                if (is_file($sigPath)) {
                    $mime = mime_content_type($sigPath) ?: 'image/png';
                    $signatureDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($sigPath));
                }
            }
        } catch (\Throwable $e) {
        }

        $pdf = Pdf::loadView('customer_invoices.pdf', [
            'invoice'          => $invoice,
            'company'          => $company,
            'totals'           => $totals,
            'currency_symbol'  => $company ? $company->currencySymbol() : '',
            'logo_url'         => $logoUrl,
            'signature_url'    => $signatureUrl,
            'logo_data_uri'    => $logoDataUri,
            'signature_data_uri' => $signatureDataUri,
        ])->setPaper('a4')->setOptions(['isRemoteEnabled' => true]);

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
            'http' => [
                'timeout' => 3,
                'user_agent' => 'Mozilla/5.0',
            ],
        ]);
        $pdf->setHttpContext($context);

        $filename = 'Invoice_' . $invoice->invoice_number . '.pdf';
        return $pdf->download($filename);
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
            'notes'          => 'nullable|string',
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
                        Storage::disk('private')->delete($invoice->document_path);
                    }
                    $path = $request->file('document')->store('customer_invoices', 'private');
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
                    Storage::disk('private')->delete($invoice->document_path);
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


    public function exportInvoices(Request $request)
    {
        $user = $request->user();
        $invoices = CustomerInvoice::where('customer_id', $user->id)
            ->with(['client:id,client_name', 'articles'])
            ->orderBy('date', 'desc')
            ->get();

        $fileName = "invoices_" . now()->format('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');

            // Add Headers
            fputcsv($file, ['Invoice#', 'Date', 'Client', 'Status', 'Article', 'Amount TTC', 'TVA', 'Payment Method', 'Category', 'Total TTC', 'Total TVA']);

            foreach ($invoices as $invoice) {
                $clientName = $invoice->client->client_name ?? 'N/A';
                foreach ($invoice->articles as $article) {
                    fputcsv($file, [
                        $invoice->invoice_number,
                        $invoice->date,
                        $clientName,
                        $invoice->status,
                        $article->designation ?? '',
                        $article->total_price_ht,
                        $article->tva_percentage,
                        $invoice->payment_method,
                        $article->designation ?? '',
                        $article->total_price_ht,
                        $article->tva_percentage
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function storeCustomerProduct(Request $request)
    {
        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'designation'    => 'required|string|max:255',
            'unit_price_ht' => 'required|numeric|min:0',
            'tva_percent'   => 'required|numeric|min:0',
            'quantity'      => 'nullable|integer|min:1',
            'total_price_ht' => 'nullable|numeric|min:0',
        ]);

        $validated['customer_id'] = $validated['customer_id'];


        $product = CustomerProduct::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer product created successfully.',
            'data'    => $product
        ], 201);
    }


    public function getCustomerProducts(Request $request)
    {
        $user = $request->user();
        $like = $request->query('like');

        $products = CustomerProduct::where('customer_id', $user->id)
            ->when($like, function ($query, $like) {
                return $query->where('designation', 'like', "%{$like}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Customer products retrieved successfully.',
            'data'    => $products
        ], 200);
    }


    public function viewSingleCustomerProduct(Request $request, $id)
    {
        $user = $request->user();

        $product = CustomerProduct::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Customer product not found or does not belong to the customer.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer product retrieved successfully.',
            'data'    => $product
        ], 200);
    }


    public function updateCustomerProduct(Request $request, $id)
    {
        $user = $request->user();

        $product = CustomerProduct::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Customer product not found or does not belong to the customer.'
            ], 404);
        }

        $validated = $request->validate([
            'designation'    => 'sometimes|required|string|max:255',
            'unit_price_ht' => 'sometimes|required|numeric|min:0',
            'tva_percent'   => 'sometimes|required|numeric|min:0',
            'quantity'      => 'nullable|integer|min:1',
            'total_price_ht' => 'nullable|numeric|min:0',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer product updated successfully.',
            'data'    => $product
        ], 200);
    }


    public function deleteCustomerProduct(Request $request, $id)
    {
        $user = $request->user();

        $product = CustomerProduct::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Customer product not found or does not belong to the customer.'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer product deleted successfully.'
        ], 200);
    }


    public function sendToAccountant(Request $request)
    {
        $request->validate([
            'to'         => 'required|email',
            'subject'    => 'required|string',
            'message'    => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        // 1. Authenticate SMTP
        Utility::getSMTPDetails(1);
        $settings = Utility::settings();

        // 2. Get Authenticated Customer Data
        $customer = $request->user();

        // 3. Prepare detailed data array
        $details = [
            'customer_name'  => $customer ? $customer->name : 'Guest Customer',
            'customer_email' => $customer ? $customer->email : $request->email ?? 'N/A',
            'subject'        => $request->subject,
            'message'        => $request->message,
            'from_email'     => $settings['mail_username'], // System sender
            'has_attachment' => $request->hasFile('attachment')
        ];

        try {
            \Mail::to($request->to)->send(
                new \App\Mail\AccountantContactMail($details, $request->file('attachment'))
            );

            return response()->json(['success' => true, 'message' => 'Email sent to accountant.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
