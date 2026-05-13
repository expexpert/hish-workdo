<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Utility;
use App\Models\InvoiceArticle;
use Illuminate\Validation\ValidationException;
use App\Models\CustomerProduct;
use App\Models\ProductService;
use Illuminate\Support\Facades\URL;
use App\Models\CustomerMonthStatus;
use App\Models\Invoice;
use App\Models\ProductServiceUnit;
use App\Models\ProductServiceCategory;
use App\Models\CustomerQuote;
use App\Models\QuoteArticle;
use App\Models\ChartOfAccountType;
use App\Models\Revenue;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\InvoicePayment;
use App\Models\MobileUserPlan;
use App\Models\MobileUserSubscription;
use App\Models\Vender;
use App\Models\User;
use App\Models\Plan;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;




class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = $request->user();

            if ($user) {
                $cacheKey = 'customer_logged_in_' . $user->id;

                if (!cache()->has($cacheKey)) {
                    $customer = Customer::find($user->id);

                    if ($customer) {
                        $lastLogin = $customer->last_login_at;

                        if (!$lastLogin || !$lastLogin->isToday()) {
                            $customer->update([
                                'last_login_at' => now(),
                            ]);
                        }

                        cache()->put($cacheKey, true, now()->endOfDay());
                    }
                }
            }

            return $next($request);
        });
    }

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

            'bio'              => 'nullable|string|max:1000',
            'short_bio'        => 'nullable|string|max:255',
            'ice_number'       => 'nullable|string|max:255',
            'rc_number'        => 'nullable|string|max:255',
            'patent_number'    => 'nullable|string|max:255',
            'if_number'        => 'nullable|string|max:255',
            'cnss'             => 'nullable|string|max:255',
            'rib'              => 'nullable|string|max:255',
            'company_type'     => 'nullable|string|max:255',
            'company_color'    => 'nullable|string|max:255',
            'contact'          => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:255',
            'billing_name'     => 'nullable|string|max:255',
            'billing_phone'    => 'nullable|string|max:20',
            'vat_number'       => 'nullable|string|max:255',
            'billing_address'  => 'nullable|string|max:255',
            'billing_zip'      => 'nullable|string|max:20',
            'billing_city'     => 'nullable|string|max:100',
            'lang'             => 'nullable|string|max:10',
            'bot_lang'         => 'nullable|string|max:10',

            // These can be present and empty (null), or missing entirely
            'website'          => 'nullable|string|max:255',
            'notes'            => 'nullable|string|max:1000',
            'customer_type'    => 'nullable|string|max:255',
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
        $userName = $user->name;
        $is_enable_login = $user->is_enable_login;

        $companyName = $user->company()->name ?? 'company';

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $clientId = $request->query('client_id');
        $supplierId = $request->query('supplier_id');

        $monthStart = now()->copy()->startOfMonth();
        $monthEnd = now()->copy()->endOfMonth();

        $plan = MobileUserPlan::find($user->mobile_user_plan_id);
        if (!$plan) {
            $plan = MobileUserPlan::where('slug', 'free')->first();
        }

        // 🔥 COMMON FORMULAS
        $net = "(invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0))";
        $vat = "($net * COALESCE(taxes.rate, 0) / 100)";
        $ttc = "($net + $vat)";

        // Counts
        $unpaidInvoicesCount = CustomerInvoice::where('customer_id', $user->id)
            ->where('status', 'issued')
            ->when($clientId, fn($q, $id) => $q->where('client_id', $id))
            ->count();

        $expiredInvoicesCount = CustomerInvoice::where('customer_id', $user->id)
            ->where('status', 'issued')
            ->whereDate('due_date', '<', now())
            ->when($clientId, fn($q, $id) => $q->where('client_id', $id))
            ->count();

        $sentQuotesCount = CustomerQuote::where('customer_id', $user->id)
            ->where('status', 'sent')
            ->when($clientId, fn($q, $id) => $q->where('client_id', $id))
            ->count();

        $unreadDocumentsCount = ClientNotification::where('customer_id', $user->id)
            ->where('is_read', false)
            ->where('data', 'like', '%"document_notification"%')
            ->count();

        $hasStatement = ClientBankStatement::where('customer_id', $user->id)
            ->where('month_year', now()->format('m-Y'))
            ->exists();

        $hasLastMonthStatement = ClientBankStatement::where('customer_id', $user->id)
            ->where('month_year', now()->subMonth()->format('m-Y'))
            ->exists();

        $missingBankStatementCount = $hasStatement ? 0 : 1;

        $currentMonthInvoice = CustomerInvoice::where('customer_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->when($clientId, fn($q, $id) => $q->where('client_id', $id))
            ->count();

        $currentMonthExpense = CustomerExpense::where('customer_id', $user->id)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->when($supplierId, fn($q, $id) => $q->where('supplier_id', $id))
            ->count();

        $unreadNotificationsCount = ClientNotification::where('customer_id', $user->id)
            ->where('is_read', false)
            ->count();

        $statementScore = $hasStatement ? 40 : 0;
        $invoiceExpenseScore = ($currentMonthInvoice + $currentMonthExpense) > 0 ? 30 : 0;
        $notificationScore = $unreadNotificationsCount > 0 ? 30 : 0;

        $unpaidInvoiceSum = CustomerInvoice::leftJoin('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
            ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
            ->where('customer_invoices.customer_id', $user->id)
            ->when($clientId, fn($q, $id) => $q->where('customer_invoices.client_id', $id))
            ->selectRaw("ROUND(SUM(CASE WHEN UPPER(status) = 'ISSUED' THEN $ttc ELSE 0 END), 2) as total_unpaid_sum")
            ->first();

        $expiredInvoiceSum = CustomerInvoice::leftJoin('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
            ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
            ->where('customer_invoices.customer_id', $user->id)
            ->when($clientId, fn($q, $id) => $q->where('customer_invoices.client_id', $id))
            ->whereDate('customer_invoices.due_date', '<', now())
            ->selectRaw("ROUND(SUM(CASE WHEN UPPER(status) = 'ISSUED' THEN $ttc ELSE 0 END), 2) as total_expired_sum")
            ->first();

        $sentQuoteSum = CustomerQuote::leftJoin('quotes_articles', 'customer_quotes.id', '=', 'quotes_articles.quotes_id')
            ->leftJoin('taxes', 'quotes_articles.tva_percentage', '=', 'taxes.id')
            ->where('customer_quotes.customer_id', $user->id)
            ->where('customer_quotes.status', 'sent')
            ->when($clientId, fn($q, $id) => $q->where('customer_quotes.client_id', $id))
            ->select(
                DB::raw("ROUND(SUM((quotes_articles.total_price_ht - COALESCE(quotes_articles.discount, 0)) * (1 + COALESCE(taxes.rate, 0) / 100)), 2) as sent_quote_sum"),
            )->first();

        $invoiceStats = CustomerInvoice::leftJoin('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
            ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
            ->where('customer_invoices.customer_id', $user->id)
            ->when($dateFrom, fn($q, $df) => $q->whereDate('customer_invoices.date', '>=', $df))
            ->when($dateTo, fn($q, $dt) => $q->whereDate('customer_invoices.date', '<=', $dt))
            ->when($clientId, fn($q, $id) => $q->where('customer_invoices.client_id', $id))
            ->select(
                DB::raw("ROUND(SUM(CASE WHEN UPPER(status) IN ('ISSUED','PAID') THEN $ttc ELSE 0 END), 2) as total_issued_paid_sum"),
                DB::raw("ROUND(SUM(CASE WHEN UPPER(status) IN ('ISSUED','PAID') THEN $net ELSE 0 END), 2) as total_ht_sum"),
                DB::raw("ROUND(SUM(CASE WHEN UPPER(status) = 'PAID' THEN $ttc ELSE 0 END), 2) as total_paid_sum"),
                DB::raw("ROUND(SUM(CASE WHEN UPPER(status) = 'ISSUED' THEN $ttc ELSE 0 END), 2) as total_issued_sum"),
                DB::raw("ROUND(SUM(CASE WHEN UPPER(status) = 'PAID' THEN $vat ELSE 0 END), 2) as vat_collected"),
                DB::raw("COUNT(DISTINCT CASE WHEN UPPER(status) = 'PAID' THEN customer_invoices.id END) as total_paid_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN UPPER(status) = 'ISSUED' THEN customer_invoices.id END) as total_issued_count"),
                DB::raw("COUNT(DISTINCT customer_invoices.client_id) as total_clients_count")
            )->first();

        $totalRevenue = Revenue::where('customer_id', $user->id)->sum('amount');

        $quoteStats = CustomerQuote::leftJoin('quotes_articles', 'customer_quotes.id', '=', 'quotes_articles.quotes_id')
            ->leftJoin('taxes', 'quotes_articles.tva_percentage', '=', 'taxes.id')
            ->where('customer_quotes.customer_id', $user->id)
            ->when($dateFrom, fn($q, $df) => $q->whereDate('customer_quotes.date', '>=', $df))
            ->when($dateTo, fn($q, $dt) => $q->whereDate('customer_quotes.date', '<=', $dt))
            ->select(
                DB::raw("ROUND(SUM((quotes_articles.total_price_ht - COALESCE(quotes_articles.discount, 0)) * (1 + COALESCE(taxes.rate, 0) / 100)), 2) as total_quote_sum"),
                DB::raw("COUNT(DISTINCT customer_quotes.id) as total_quote_count")
            )
            ->first();

        // Expenses
        $expenseStats = CustomerExpense::where('customer_id', $user->id)
            ->when($dateFrom, fn($q, $df) => $q->whereDate('date', '>=', $df))
            ->when($dateTo, fn($q, $dt) => $q->whereDate('date', '<=', $dt))
            ->when($supplierId, fn($q, $id) => $q->where('supplier_id', $id))
            ->select(
                DB::raw("SUM(total_ttc) as total_sum"),
                DB::raw("SUM(total_tva) as total_tva"),
                DB::raw("ROUND(SUM(COALESCE(total_ttc, ttc, 0)), 2) as bot_total_sum"),
                DB::raw("ROUND(SUM(COALESCE(total_tva, tva, 0)), 2) as bot_total_tva"),
                DB::raw("COUNT(id) as total_expenses_count")
            )->first();

        $totalVatPayable = ($invoiceStats->vat_collected ?? 0) - ($expenseStats->total_tva ?? 0);

        // 🔁 Period comparison (FIXED)
        $getPeriodicStats = function ($range) use ($user, $ttc, $net, $vat) {
            $inv = CustomerInvoice::leftJoin('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
                ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
                ->where('customer_invoices.customer_id', $user->id)
                ->whereBetween('customer_invoices.date', $range)
                ->select(
                    DB::raw("SUM(CASE WHEN UPPER(status) = 'PAID' THEN $ttc ELSE 0 END) as paid"),
                    DB::raw("SUM(CASE WHEN UPPER(status) = 'PAID' THEN $vat ELSE 0 END) as vat")
                )->first();

            $rev = Revenue::where('customer_id', $user->id)
                ->whereBetween('date', $range)
                ->selectRaw("SUM(amount) as total")
                ->first();

            $exp = CustomerExpense::where('customer_id', $user->id)
                ->whereBetween('date', $range)
                ->selectRaw("SUM(total_ttc) as total, SUM(total_tva) as tva")
                ->first();

            return (object)[
                'paid' => (float)($inv->paid ?? 0) + (float)($rev->total ?? 0),
                'expense' => (float)($exp->total ?? 0),
                'vat' => (float)($inv->vat ?? 0) - (float)($exp->tva ?? 0)
            ];
        };

        $current = $getPeriodicStats([now()->startOfMonth(), now()->endOfMonth()]);
        $previous = $getPeriodicStats([now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);

        $calcTrend = function ($cur, $prev) {
            if ($prev == 0) return $cur > 0 ? 100 : 0;
            return round((($cur - $prev) / $prev) * 100, 2);
        };

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully.',
            'data' => [
                'userName' => $userName,
                'companyName' => $companyName,
                'total_issued_paid_sum' => (float) ($invoiceStats->total_issued_paid_sum ?? 0),
                'total_paid_sum' => (float) ($invoiceStats->total_paid_sum ?? 0) + (float) ($totalRevenue ?? 0),
                'total_expenses_sum' => (float) ($expenseStats->total_sum ?? 0),
                'bot_total_sum' => (float) ($expenseStats->bot_total_sum ?? 0),
                'vat_collected' => (float) ($invoiceStats->vat_collected ?? 0),
                'total_vat_payable' => (float) $totalVatPayable,
                'total_issued_count' => $invoiceStats->total_issued_count,
                'total_paid_count' => $invoiceStats->total_paid_count,
                'total_quote_count' => $quoteStats->total_quote_count,
                'total_expenses_count' => $currentMonthExpense,
                'bank_statements_count' => ClientBankStatement::where('customer_id', $user->id)->count(),
                'total_pending_review_count' => CustomerInvoice::where('customer_id', $user->id)->where('review_status', 'PENDING')->count(),
                'total_clients_count' => Customer::where('created_by', $user->creatorId())->count() + \App\Models\Vender::where('created_by', $user->creatorId())->count(),
                'total_issued_sum' => (float) ($invoiceStats->total_issued_sum ?? 0),
                'total_quote_sum' => (float) ($quoteStats->total_quote_sum ?? 0),
                'total_expenses_vat' => (float) ($expenseStats->total_tva ?? 0),

                'total_paid_percentage_change' => $calcTrend($current->paid, $previous->paid),
                'total_expenses_percentage_change' => $calcTrend($current->expense, $previous->expense),
                'total_vat_payable_percentage_change' => $calcTrend($current->vat, $previous->vat),

                'hasStatement' => $hasStatement,
                'hasLastMonthStatement' => $hasLastMonthStatement,
                'unpaidInvoicesCount' => $unpaidInvoicesCount,
                'unpaidInvoiceSum' => (float) ($unpaidInvoiceSum->total_unpaid_sum ?? 0),
                'unreadDocumentsCount' => $unreadDocumentsCount,
                'total_pending_actions' => $missingBankStatementCount + $unpaidInvoicesCount + $unreadDocumentsCount,
                'total_progress_score' => $statementScore + $invoiceExpenseScore + $notificationScore,
                'expiredInvoicesCount' => $expiredInvoicesCount,
                'expiredInvoiceSum' => (float) ($expiredInvoiceSum->total_expired_sum ?? 0),
                'sentQuotesCount' => $sentQuotesCount,
                'sentQuoteSum' => (float) ($sentQuoteSum->sent_quote_sum ?? 0),


                'is_enable_login' => $is_enable_login,
                'whatsapp_bot_enabled' => $plan->whatsapp_bot_enabled,
            ]
        ], 200);
    }


    public function getDashboardGraphData(Request $request)
    {
        $user = $request->user();
        $year = $request->get('year', date('Y'));

        $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endDate = $startDate->copy()->endOfYear();

        $ttc = "(invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0)) * (1 + COALESCE(taxes.rate, 0) / 100)";

        $invoices = CustomerInvoice::join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
            ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
            ->where('customer_invoices.customer_id', $user->id)
            ->whereIn('customer_invoices.status', ['issued', 'paid'])
            ->whereBetween('customer_invoices.date', [$startDate, $endDate])
            ->select(
                DB::raw('MONTH(customer_invoices.date) as month'),
                DB::raw("ROUND(SUM($ttc), 2) as total")
            )
            ->groupBy('month')
            ->pluck('total', 'month');

        $expenses = CustomerExpense::where('customer_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(ttc) as total')
            )
            ->groupBy('month')
            ->pluck('total', 'month');

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


    public function getAnalyseRapide(Request $request): JsonResponse
    {
        $user = $request->user();

        $now = Carbon::now();
        $startOfCurrentMonth = $now->copy()->startOfMonth();
        $startOfPreviousMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfPreviousMonth = $now->copy()->subMonth()->endOfMonth();

        // 🔥 COMMON FORMULA (TTC)
        $ttc = "(invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0)) * (1 + COALESCE(taxes.rate, 0) / 100)";

        // Expenses (already TTC ✔)
        $currentMonthExpenses = CustomerExpense::where('customer_id', $user->id)
            ->whereBetween('date', [$startOfCurrentMonth, $now])
            ->sum('total_ttc');

        $previousMonthExpenses = CustomerExpense::where('customer_id', $user->id)
            ->whereBetween('date', [$startOfPreviousMonth, $endOfPreviousMonth])
            ->sum('total_ttc');

        $expenseVariation = $previousMonthExpenses > 0
            ? (($currentMonthExpenses - $previousMonthExpenses) / $previousMonthExpenses) * 100
            : 0;

        // ✅ Pending invoices (TTC)
        $pendingData = CustomerInvoice::where('customer_id', $user->id)
            ->where('status', 'issued')
            ->leftJoin('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
            ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
            ->selectRaw("COUNT(DISTINCT customer_invoices.id) as count, ROUND(SUM($ttc), 2) as amount")
            ->first();

        // ✅ Revenue (TTC after discount)
        $currentRevenue = InvoiceArticle::join('customer_invoices', 'invoice_articles.invoice_id', '=', 'customer_invoices.id')
            ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
            ->where('customer_invoices.customer_id', $user->id)
            ->whereBetween('customer_invoices.date', [$startOfCurrentMonth, $now])
            ->selectRaw("ROUND(SUM($ttc), 2) as total")
            ->value('total');

        $previousRevenue = InvoiceArticle::join('customer_invoices', 'invoice_articles.invoice_id', '=', 'customer_invoices.id')
            ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
            ->where('customer_invoices.customer_id', $user->id)
            ->whereBetween('customer_invoices.date', [$startOfPreviousMonth, $endOfPreviousMonth])
            ->selectRaw("ROUND(SUM($ttc), 2) as total")
            ->value('total');

        $revenueVariation = $previousRevenue > 0
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'expenses_alert' => [
                    'is_higher' => $currentMonthExpenses > $previousMonthExpenses,
                    'variation_percentage' => round($expenseVariation, 2),
                    'current' => $currentMonthExpenses,
                    'previous' => $previousMonthExpenses,
                ],
                'pending_invoices' => [
                    'count' => $pendingData->count ?? 0,
                    'total_amount' => $pendingData->amount ?? 0,
                ],
                'performance_alert' => [
                    'is_good' => $revenueVariation > 0,
                    'variation_percentage' => round($revenueVariation, 2),
                    'current_revenue' => $currentRevenue ?? 0,
                    'previous_revenue' => $previousRevenue ?? 0,
                ]
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


    public function getSubscriptionStatus(Request $request): JsonResponse
    {
        $user = $request->user();

        $encryptedId = Crypt::encryptString($user->id);

        $url = URL::temporarySignedRoute(
            'subscription.upgrade',
            now()->addMinutes(30),
            ['uid' => $encryptedId]
        );

        $data = [];

        $data['is_b2c'] = $user->is_b2c;
        $data['subscription'] = MobileUserSubscription::where('customer_id', $user->id)->latest()->first();
        $data['plan'] = MobileUserPlan::where('id', $user->mobile_user_plan_id)->select('id', 'name', 'slug')->first() ?? 'free';

        // Fetch the plan limits
        $plan = MobileUserPlan::find($user->mobile_user_plan_id);
        if (!$plan) {
            $plan = MobileUserPlan::where('slug', 'free')->first();
        }

        // Calculate usage
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $invoiceUsed = CustomerInvoice::where('customer_id', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $quoteUsed = CustomerQuote::where('customer_id', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $expenseUsed = CustomerExpense::where('customer_id', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $receiptsUsed = Revenue::where('customer_id', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $totalClients = CustomerClient::where('customer_id', $user->id)->count();
        $totalSuppliers = Vender::where('customer_id', $user->id)->count();

        $models = [
            CustomerInvoice::class,
            CustomerQuote::class,
            CustomerExpense::class,
            Revenue::class,
        ];

        $ocrUsed = 0;

        foreach ($models as $model) {
            $ocrUsed += $model::where('customer_id', $user->id)
                ->where('is_ocr', true)
                ->count();
        }

        $data['usage'] = [
            'invoices' => [
                'used' => $invoiceUsed,
                'limit' => $plan ? $plan->invoice_limit : 0,
                'remaining' => $plan ? ($plan->invoice_limit === null ? -1 : max(0, $plan->invoice_limit - $invoiceUsed)) : 0,
            ],
            'quotes' => [
                'used' => $quoteUsed,
                'limit' => $plan ? $plan->quote_limit : 0,
                'remaining' => $plan ? ($plan->quote_limit === null ? -1 : max(0, $plan->quote_limit - $quoteUsed)) : 0,
            ],
            'expenses' => [
                'used' => $expenseUsed,
                'limit' => $plan ? $plan->expense_limit : 0,
                'remaining' => $plan ? ($plan->expense_limit === null ? -1 : max(0, $plan->expense_limit - $expenseUsed)) : 0,
            ],
            'receipts' => [
                'used' => $receiptsUsed,
                'limit' => $plan ? $plan->receipt_limit : 0,
                'remaining' => $plan ? ($plan->receipt_limit === null ? -1 : max(0, $plan->receipt_limit - $receiptsUsed)) : 0,
            ],
            'ocr' => [
                'used' => $ocrUsed,
                'limit' => $plan ? $plan->ocr_limit : 0,
                'remaining' => $plan ? ($plan->ocr_limit === null ? -1 : max(0, $plan->ocr_limit - $ocrUsed)) : 0,
            ],
            'storage' => [
                'used_mb' => (int) ($user->storage_used_mb ?? 0),
                'limit_mb' => $plan ? (int) $plan->storage_limit_mb : 0,
                'remaining_mb' => $plan ? max(0, $plan->storage_limit_mb - ($user->storage_used_mb ?? 0)) : 0,
            ],
            'clients' => [
                'used' => $totalClients,
                'limit' => $plan ? $plan->client_limit : 0,
                'remaining' => $plan ? ($plan->client_limit === null ? -1 : max(0, $plan->client_limit - $totalClients)) : 0,
            ],
            'suppliers' => [
                'used' => $totalSuppliers,
                'limit' => $plan ? $plan->supplier_limit : 0,
                'remaining' => $plan ? ($plan->supplier_limit === null ? -1 : max(0, $plan->supplier_limit - $totalSuppliers)) : 0,
            ],
        ];

        $data['features'] = [
            'whatsapp_bot_enabled' => $plan->whatsapp_bot_enabled,
            'export_enabled' => $plan->export_enabled,
            'logo' => $plan->logo,
        ];

        $data['upgrade_url'] = $url;

        return response()->json([
            'success' => true,
            'message' => 'Subscription status retrieved successfully.',
            'data'    => $data,
        ], 200);
    }


    public function getOnboardingChecklist(Request $request): JsonResponse
    {
        $user = $request->user();

        $requiredFields = ['billing_name', 'ice_number', 'rc_number', 'patent_number', 'if_number', 'cnss', 'rib'];

        $isCompanySetupComplete = collect($requiredFields)->every(fn($field) => !empty($user->$field));

        $steps = [
            'company-setup' => $isCompanySetupComplete,
            'first-client'  => CustomerClient::where('customer_id', $user->id)->exists(),
            'first-invoice' => CustomerInvoice::where('customer_id', $user->id)->exists(),
            'add-supplier'  => Vender::where('customer_id', $user->id)->exists(),
            'first-expense' => CustomerExpense::where('customer_id', $user->id)->exists(),
        ];

        $checklist = collect($steps)->map(function ($isCompleted, $key) {
            return [
                'id'     => $key,
                'status' => $isCompleted ? 'completed' : 'pending'
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Onboarding checklist retrieved successfully.',
            'data'    => $checklist
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

        $settings = Utility::settingsById($accountant->creatorId());
        $company_logo = isset($settings['company_logo_dark']) && !empty($settings['company_logo_dark']) ? $settings['company_logo_dark'] : 'logo-dark.png';

        $accountantData = $accountant->toArray();
        $accountantData['company_logo'] = Utility::get_file('uploads/logo/') . $company_logo;

        return response()->json([
            'success' => true,
            'message' => 'Accountant information retrieved successfully.',
            'data'    => $accountantData
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
        $documentType = $request->get('documentType');

        // 1. Fetch filtered ClientNotifications
        $notifications = ClientNotification::where('customer_id', $user->id)
            ->where('data', 'like', '%"document_notification"%')
            ->when($documentType && $documentType !== 'all', function ($query) use ($documentType) {
                return $query->where('title', $documentType);
            })
            ->orderBy('created_at', 'desc')
            // ->limit(20)
            ->get();

        $documents = $notifications;

        // 2. If 'all', merge with Bank Statements
        if ($documentType === 'all') {
            $bankStatements = ClientBankStatement::where('customer_id', $user->id)
                ->orderBy('created_at', 'desc')
                // ->limit(20)
                ->get();

            // Merge and re-sort by created_at if you want a unified timeline
            $documents = $notifications->concat($bankStatements)
                ->sortByDesc('created_at')
                ->values(); // Reset keys
            // ->take(20); // Keep the limit consistent
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer documents retrieved successfully.',
            'data'    => [
                'documents' => $documents
            ]
        ], 200);
    }


    public function getDocumentsData(Request $request)
    {
        $user = $request->user();

        // 1. Get documents from ClientNotification
        $notifications = ClientNotification::where('customer_id', $user->id)
            ->where('data', 'like', '%document_notification%')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Get bank statements
        $bankStatements = ClientBankStatement::where('customer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Categorize
        $juridiques = $notifications->where('title', 'juridiques');
        $comptables = $notifications->where('title', 'comptables');
        $relevesBancaires = $bankStatements;

        // 4. Calculate total count and size
        $totalCount = $notifications->count() + $bankStatements->count();
        $totalSizeBytes = $this->calculateSize($notifications, 'document') + $this->calculateSize($bankStatements, 'file_path');
        $totalSizeFormatted = $this->formatSize($totalSizeBytes);

        // 5. Recent 3 documents
        $allDocs = collect();
        foreach ($notifications as $doc) {
            $allDocs->push([
                'id'         => $doc->id,
                'name'       => $doc->message ?: basename($doc->document),
                'type'       => $doc->title, // juridiques or comptables
                'created_at' => $doc->created_at,
                'size'       => $this->formatSize(Storage::disk('public')->exists($doc->document) ? Storage::disk('public')->size($doc->document) : 0),
                'url'        => $doc->document_url,
            ]);
        }
        foreach ($bankStatements as $doc) {
            $allDocs->push([
                'id'         => $doc->id,
                'name'       => $doc->month_year ? "Statement - " . $doc->month_year : basename($doc->file_path),
                'type'       => 'Bank statements',
                'created_at' => $doc->created_at,
                'size'       => $this->formatSize(Storage::disk('private')->exists($doc->file_path) ? Storage::disk('private')->size($doc->file_path) : 0),
                'url'        => $doc->file_url,
            ]);
        }
        $recentDocs = $allDocs->sortByDesc('created_at')->take(3)->values();

        // 6. Categories data
        $categories = [
            [
                'name'  => 'Documents legal',
                'count' => $juridiques->count(),
                'size'  => $this->formatSize($this->calculateSize($juridiques, 'document')),
                'type'  => 'juridiques',
                'created_at' => $juridiques->max('created_at')
            ],
            [
                'name'  => 'Documents accountants',
                'count' => $comptables->count(),
                'size'  => $this->formatSize($this->calculateSize($comptables, 'document')),
                'type'  => 'comptables',
                'created_at' => $comptables->max('created_at')
            ],
            [
                'name'  => 'Bank statements',
                'count' => $relevesBancaires->count(),
                'size'  => $this->formatSize($this->calculateSize($relevesBancaires, 'file_path')),
                'type'  => 'releves_bancaires',
                'created_at' => $relevesBancaires->max('created_at')
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data'   => [
                'total_documents'  => $totalCount,
                'total_size'       => $totalSizeFormatted,
                'total_categories' => 3,
                'recent_documents' => $recentDocs,
                'categories'       => $categories
            ]
        ]);
    }

    private function calculateSize($collection, $field)
    {
        $size = 0;
        foreach ($collection as $item) {
            if ($item->$field && (Storage::disk('public')->exists($item->$field) || Storage::disk('private')->exists($item->$field))) {
                if ($field === 'file_path') {
                    $size += Storage::disk('private')->size($item->$field);
                } else {
                    $size += Storage::disk('public')->size($item->$field);
                }
            }
        }
        return $size;
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    public function downloadDocument($id, Request $request)
    {
        $notification = ClientNotification::where('customer_id', $request->user()->id)
            ->findOrFail($id);

        if (!$notification->document || !Storage::disk('public')->exists($notification->document)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        if (! $notification->is_read) {
            $notification->is_read = true;
            $notification->save();
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
            'payment_receipt'  => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:20500',
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
            'statement' => 'required|mimes:pdf,csv,xls,xlsx,jpg,jpeg,png|max:20500',
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

        $this->notifyAccountant($request->user(), 'Bank Statement', null, $request->month_year);

        // Generate temporary signed URL for browser access (Valid for 24h)
        $downloadUrl = $statement->file_path ? URL::temporarySignedRoute(
            'api.download.file.public',
            now()->addHours(24),
            ['id' => $statement->id, 'customer_id' => $statement->customer_id, 'type' => 'statement']
        ) : null;

        $statement->download_url = $downloadUrl;

        return response()->json([
            'message' => $message,
            'data' => $statement
        ], $status);
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
            'email' => 'nullable|email|unique:customer_clients,email',
            'telephone' => 'nullable|string|max:20',
            'postal_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
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
        $today = now()->format('Y-m-d');
        $sort = $request->query('sort');

        $query = CustomerClient::where('customer_id', $user->id);

        if ($like) {
            $query->where(function ($q) use ($like) {
                $q->where('company_name', 'like', "%$like%")
                    ->orWhere('client_name', 'like', "%$like%");
            });
        }

        $clients = $query
            ->addSelect([
                'total_revenue_ht' => function ($q) {
                    $q->from('invoice_articles')
                        ->join('customer_invoices', 'invoice_articles.invoice_id', '=', 'customer_invoices.id')
                        ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
                        ->whereColumn('customer_invoices.client_id', 'customer_clients.id')
                        ->where('customer_invoices.status', 'issued')
                        ->selectRaw("ROUND(SUM((invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0)) * (1 + COALESCE(taxes.rate, 0) / 100)), 2)");
                }
            ])

            ->withCount(['invoices as late_invoices_count' => function ($q) use ($today) {
                $q->where('due_date', '<', $today)
                    ->where('status', '!=', 'paid');
            }]);

        if ($sort === 'recent') {
            $clients->withMax('invoices', 'created_at')
                ->orderByRaw('invoices_max_created_at IS NULL, invoices_max_created_at DESC');
        } else {
            $clients->orderByRaw('CASE WHEN late_invoices_count > 0 THEN 0 ELSE 1 END')
                ->orderBy('late_invoices_count', 'desc')
                ->orderBy('total_revenue_ht', 'desc');
        }

        $clients = $clients->with(['invoices' => function ($q) {
            $q->where('status', 'issued')->with('articles');
        }])
            ->get();

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
            ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
            ->selectRaw("ROUND(SUM((invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0))* (1 + COALESCE(taxes.rate, 0) / 100)), 2) as total")
            ->value('total');

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
            'client_name' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|email|unique:customer_clients,email,' . $client->id,
            'telephone' => 'nullable|string|max:20',
            'postal_code' => 'sometimes|nullable|string|max:20',
            'city' => 'sometimes|nullable|string|max:100',
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
            'email' => 'required|email|unique:venders,email',
            'telephone' => 'required|string|max:20',
            'postal_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'commercial_register' => 'nullable|string|max:255',
            'ice' => 'nullable|string|max:255',
        ]);

        $validated['customer_id'] = $request->user()->id;

        $objVendor    = User::find(auth()->user()->companyId());
        $creator      = User::find(auth()->user()->companyId());
        $total_vendor = $creator->countVenders();
        $plan         = Plan::find($creator->plan);

        // if ($total_vendor < $plan->max_venders || $plan->max_venders == -1) {
        $vender = new Vender();
        $vender->customer_id = $validated['customer_id'];
        $vender->company_name = $validated['company_name'];
        $vender->name = $validated['supplier_name'];
        $vender->email = $validated['email'];
        $vender->contact = $validated['telephone'];
        $vender->billing_zip = $validated['postal_code'] ?? '';
        $vender->billing_city = $validated['city'] ?? '';
        $vender->commercial_register = $validated['commercial_register'] ?? '';
        $vender->ice_number = $validated['ice'] ?? '';

        $vender->created_by  = auth()->user()->companyId();
        $vender->vender_id   = $this->venderNumber();
        $vender->is_enable_login =  0;

        $vender->save();
        // } else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Vendor limit reached for your current plan. Please upgrade to add more vendors.'
        //     ], 403);
        // }

        return response()->json([
            'success' => true,
            'message' => 'Customer supplier created successfully.',
            'data'    => $vender
        ], 201);
    }

    function venderNumber()
    {
        $latest = Vender::where('created_by', '=', auth()->user()->companyId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->vender_id + 1;
    }


    public function getCustomerSuppliers(Request $request)
    {
        $user = $request->user();
        $like = $request->query('like');
        $today = now()->format('Y-m-d');
        $company_id = auth()->user()->companyId();


        $query = Vender::where('customer_id', $user->id)->where('created_by', $company_id);

        if ($like) {
            $query->where(function ($q) use ($like) {
                $q->where('company_name', 'like', "%$like%")
                    ->orWhere('name', 'like', "%$like%");
            });
        }

        $sort = $request->query('sort');

        $suppliers = $query
            // 1. Total Sum of all expenses
            ->withSum('expenses as total_ttc', 'total_ttc')
            ->withCount('expenses as expenses_count');

        if ($sort === 'recent') {
            // Sort by the creation date of the most recent expense
            $suppliers->withMax('expenses', 'created_at')
                ->orderByRaw('expenses_max_created_at IS NULL, expenses_max_created_at DESC');
        }

        $suppliers = $suppliers->with('expenses')->get();

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
        $supplier = Vender::where('id', $id)
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
        $supplier = Vender::where('id', $id)->where('customer_id', $user->id)->first();

        if (! $supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Customer supplier not found or does not belong to the customer.'
            ], 404);
        }

        $validated = $request->validate([
            'company_name' => 'sometimes|required|string|max:255',
            'supplier_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:venders,email,' . $supplier->id,
            'telephone' => 'sometimes|required|string|max:20',
            'postal_code' => 'sometimes|nullable|string|max:20',
            'city' => 'sometimes|nullable|string|max:100',
            'commercial_register' => 'nullable|string|max:255',
            'ice' => 'nullable|string|max:255',
        ]);

        $vender = Vender::find($supplier->id);
        $vender->company_name = $validated['company_name'] ?? $vender->company_name;
        $vender->name = $validated['supplier_name'] ?? $vender->name;
        $vender->email = $validated['email'] ?? $vender->email;
        $vender->telephone = $validated['telephone'] ?? $vender->telephone;
        $vender->billing_zip = $validated['postal_code'] ?? $vender->billing_zip;
        $vender->billing_city = $validated['city'] ?? $vender->billing_city;
        $vender->commercial_register = $validated['commercial_register'] ?? $vender->commercial_register;
        $vender->ice_number = $validated['ice'] ?? $vender->ice_number;
        $vender->save();


        return response()->json([
            'success' => true,
            'message' => 'Customer supplier updated successfully.',
            'data'    => $supplier
        ], 200);
    }


    public function deleteCustomerSupplier(Request $request, $id)
    {
        $user = $request->user();
        $supplier = Vender::where('id', $id)->where('customer_id', $user->id)->first();

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

        $supplier = Vender::where('id', $id)
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
                'supplier_id'    => 'required|exists:venders,id',
                'file'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20500',
                'date'           => 'required|date',
                'ttc'            => 'required|numeric|min:0',
                'tva'            => 'nullable|numeric|min:0',
                'payment_method' => 'required|string|max:255',
                'category_id'    => 'required|exists:customer_categories,id',
                'total_ttc'      => 'nullable|numeric|min:0',
                'total_tva'      => 'nullable|numeric|min:0',
                'notes'          => 'nullable|string',
                'reference'      => 'nullable|string',
                'is_ocr'         => 'nullable|boolean',
            ]);

            if (!empty($request->file)) {
                $image_size = $request->file('file')->getSize();
                $user = $request->user();

                // $result = $user->is_b2c
                //     ? Utility::updateB2CStorageLimit($user->id, $image_size)
                //     : Utility::updateStorageLimit($user->companyId(), $image_size);

                $result = Utility::updateB2CStorageLimit($user->id, $image_size);

                if ($result != 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Storage limit exceeded. Cannot upload document.'
                    ], 400);
                }
            }

            // Ensure metrics columns are populated for dashboard sync
            if (!isset($validated['total_ttc']) || empty($validated['total_ttc'])) {
                $validated['total_ttc'] = $validated['ttc'];
            }
            if (!isset($validated['total_tva'])) {
                $validated['total_tva'] = $validated['tva'] ?? 0;
            }

            // Handle File Upload
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('expenses', 'private');
                $validated['file'] = $path;
            }

            $expense = CustomerExpense::create($validated);

            // Generate temporary signed URL for browser access (Valid for 24h)
            $downloadUrl = $expense->file ? URL::temporarySignedRoute(
                'api.download.file.public',
                now()->addHours(24),
                ['id' => $expense->id, 'customer_id' => $expense->customer_id, 'type' => 'expense']
            ) : null;

            $this->notifyAccountant($request->user(), 'Expense', $expense->ttc);

            $expense->download_url = $downloadUrl;

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
        $supplierId = $request->query('supplier_id');
        $id = $request->query('id');

        $query = CustomerExpense::where('customer_id', $user->id)
            ->with(['category:id,name', 'supplier:id,name'])
            ->orderBy('date', 'desc');

        if ($id) {
            $query->where('id', $id);
        }

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

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

        $expenses = $query->latest()->get();

        // Append signed download URLs for the bot
        $expenses->each(function ($expense) {
            $expense->download_url = URL::temporarySignedRoute(
                'api.download.file.public',
                now()->addHours(24),
                ['id' => $expense->id, 'customer_id' => $expense->customer_id]
            );
        });

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
            'supplier_id'    => 'sometimes|required|exists:venders,id',
            'file'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20500',
            'date'           => 'sometimes|required|date',
            'ttc'            => 'sometimes|required|numeric|min:0',
            'tva'            => 'nullable|numeric|min:0',
            'payment_method' => 'sometimes|required|string|max:255',
            'category_id'    => 'sometimes|required|exists:customer_categories,id',
            'total_ttc'      => 'nullable|numeric|min:0',
            'total_tva'      => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
            'reference'      => 'nullable|string',
        ]);

        // Handle File Upload
        if ($request->hasFile('file')) {
            $image_size = $request->file('file')->getSize();

            $result = Utility::updateB2CStorageLimit($user->id, $image_size);

            if ($result != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Storage limit exceeded. Cannot upload document.'
                ], 400);
            }

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
            $file_path = $expense->file;
            $user->is_b2c
                ? Utility::changeB2CStorageLimitNew($user->id, $file_path)
                : Utility::changeStorageLimitNew($user->companyId(), $file_path);
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
            fputcsv($file, ['Date', 'Amount TTC', 'TVA', 'Payment Method', 'Category', 'Discount', 'Total TTC', 'Total TVA']);

            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->date,
                    $expense->ttc,
                    $expense->tva,
                    $expense->payment_method,
                    $expense->category->name ?? 'N/A',
                    $expense->discount ?? 0,
                    $expense->total_ttc,
                    $expense->total_tva
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function duplicateExpense(Request $request, $id)
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

        $duplicateExpense = $expense->replicate();

        // ✅ Handle file duplication
        if ($expense->file && Storage::disk('private')->exists($expense->file)) {

            $originalPath = $expense->file;

            // Generate new unique file name
            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            $newFileName = 'expenses/' . Str::uuid() . '.' . $extension;

            // Copy file
            Storage::disk('private')->copy($originalPath, $newFileName);

            // Assign new file path
            $duplicateExpense->file = $newFileName;
        }

        $duplicateExpense->customer_id = $user->id;
        $duplicateExpense->save();

        return response()->json([
            'success' => true,
            'message' => 'Expense duplicated successfully.',
            'data'    => $duplicateExpense
        ], 200);
    }


    public function storeInvoice(Request $request)
    {

        // ✅ STEP 1: Validation
        $validated = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'client_id'      => 'required|exists:customer_clients,id',
            'date'           => 'required|date',
            'due_date'       => 'required|date|after:date',
            'payment_method' => 'required|string|max:255',
            'status'         => 'required|string|max:50',
            'invoice_number' => 'required|string|max:255',
            'notes'          => 'nullable|string',
            'document'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20500',
            'is_ocr'         => 'nullable|boolean',

            'articles'                    => 'sometimes|array',
            'articles.*.designation'     => 'required_with:articles|string|max:255',
            'articles.*.product_id'      => 'nullable|integer',
            'articles.*.unit_price_ht'   => 'required_with:articles|numeric|min:0',
            'articles.*.quantity'        => 'nullable|integer|min:1',
            'articles.*.total_price_ht'  => 'nullable|numeric|min:0',
            'articles.*.tva_percentage'  => 'required_with:articles|exists:taxes,id',
            'articles.*.discount'        => 'nullable|numeric|min:0|max:100',
        ]);

        if (!empty($request->document)) {
            $image_size = $request->file('document')->getSize();

            $user = $request->user();

            $result = Utility::updateB2CStorageLimit($user->id, $image_size);

            if ($result != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Storage limit exceeded. Cannot upload document.'
                ], 400);
            }
        }

        try {
            // ✅ STEP 2: Prepare header data
            $invoiceData = collect($validated)->except(['articles', 'document'])->toArray();
            $invoiceData['invoice_number'] = $this->parseNumber($validated['invoice_number']);

            $articlesData = [];
            $now = now();

            // ✅ STEP 3: Prepare articles BEFORE DB
            if (!empty($validated['articles'])) {
                foreach ($validated['articles'] as $article) {
                    $qty = $article['quantity'] ?? 1;

                    $articlesData[] = [
                        'product_id'     => $article['product_id'] ?? 1,
                        'designation'    => $article['designation'],
                        'unit_price_ht'  => $article['unit_price_ht'],
                        'quantity'       => $qty,
                        'total_price_ht' => $article['total_price_ht'] ?? ($article['unit_price_ht'] * $qty),
                        'tva_percentage' => $article['tva_percentage'],
                        'discount'       => $article['discount'] ?? 0,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ];
                }
            }


            // ✅ STEP 4: DB Transaction (FAST)
            $invoice = DB::transaction(function () use ($invoiceData, $articlesData, $request) {

                $invoice = CustomerInvoice::create($invoiceData);

                // 🚀 BULK INSERT ARTICLES
                if (!empty($articlesData)) {
                    foreach ($articlesData as &$article) {
                        $article['invoice_id'] = $invoice->id;
                    }

                    DB::table('invoice_articles')->insert($articlesData);
                }
                // 🚀 BOT FALLBACK (also BULK STYLE)
                else if ($request->has('amount')) {

                    DB::table('invoice_articles')->insert([
                        [
                            'invoice_id'     => $invoice->id,
                            'product_id'     => 1,
                            'designation'    => $request->input('notes') ?: 'Professional Services',
                            'unit_price_ht'  => $request->input('amount'),
                            'quantity'       => 1,
                            'total_price_ht' => $request->input('amount'),
                            'tva_percentage' => $request->input('vat', $request->input('tva_percentage')) ?: null,
                            'discount'       => $request->input('discount', 0),
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]
                    ]);
                }

                return $invoice;
            });

            // ✅ STEP 5: File Upload AFTER DB (important)
            if ($request->hasFile('document')) {

                $documentPath = $request->file('document')->store('customer_invoices', 'private');

                $invoice->update([
                    'document_path' => $documentPath
                ]);
            }

            // ✅ STEP 6: Generate signed URL
            $urlStart = microtime(true);

            $downloadUrl = URL::temporarySignedRoute(
                'api.download.invoice.pdf.public',
                now()->addHours(24),
                [
                    'id' => $invoice->id,
                    'customer_id' => $invoice->customer_id
                ]
            );

            $invoice->download_url = $downloadUrl;


            // ✅ STEP 7: Return (no heavy reload)
            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully.',
                'data'    => $invoice->setRelation(
                    'articles',
                    collect($validated['articles'] ?? [])
                )
            ], 201);
        } catch (\Exception $e) {


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
        $clientId = $request->query('client_id');
        $id = $request->query('id');
        $invoiceNumber = $request->query("invoice_number");
        $today = now()->startOfDay();

        $query = CustomerInvoice::where('customer_id', $user->id)
            ->with(['client:id,client_name', 'articles', 'articles.tax:id,rate,name'])
            ->orderBy('date', 'desc');

        if ($id) {
            $query->where('id', $id);
        }

        if ($invoiceNumber) {
            $query->whereRaw("REPLACE(REPLACE(REPLACE(invoice_number, '-', ''), ' ', ''), '.', '') = ?", [$invoiceNumber]);
        }
        if ($status) {
            $query->where('status', $status);
        }

        if ($clientId) {
            $query->where('client_id', $clientId);
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

        $totalAllInvoices = 0;
        $totalPaidInvoices = 0;
        $totalIssuedInvoices = 0;
        $totalCancelledInvoices = 0;
        $totalOverdueInvoices = 0;

        $invoices->each(function ($invoice) use ($today, &$totalAllInvoices, &$totalPaidInvoices, &$totalIssuedInvoices, &$totalCancelledInvoices, &$totalOverdueInvoices) {
            $totalTtc = 0;
            $totalTax = 0;
            $maxTaxRate = 0;

            foreach ($invoice->articles as $article) {
                $priceHt = $article->unit_price_ht * ($article->quantity ?? 1);
                $discount = $article->discount ?? 0;
                $priceAfterDiscount = $priceHt - $discount;

                $taxRate = $article->tax ? $article->tax->rate : 0;
                $taxAmount = round($priceAfterDiscount * $taxRate / 100, 2);

                $totalTtc += ($priceAfterDiscount + $taxAmount);
                $totalTax += $taxAmount;
                if ($taxRate > $maxTaxRate) $maxTaxRate = $taxRate;
            }

            $invoice->total_ttc = $totalTtc;
            $invoice->total_tax = $totalTax;
            $invoice->tax_rate = $maxTaxRate;

            // --- Logic for Aggregates ---
            $totalAllInvoices += $totalTtc;

            if ($invoice->status === 'paid') {
                $totalPaidInvoices += $totalTtc;
            }

            if ($invoice->status === 'issued') {
                $totalIssuedInvoices += $totalTtc;
            }

            if ($invoice->status === 'cancelled') {
                $totalCancelledInvoices += $totalTtc;
            }

            // Logic for "Issued" and "Overdue" (due_date < today)
            if ($invoice->status === 'issued' && $invoice->due_date && Carbon::parse($invoice->due_date)->lt($today)) {
                $totalOverdueInvoices += $totalTtc;
            }

            $invoice->download_url = URL::temporarySignedRoute(
                'api.download.invoice.pdf.public',
                now()->addHours(24),
                ['id' => $invoice->id, 'customer_id' => $invoice->customer_id]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Customer invoices retrieved successfully.',
            'data'    => [
                'invoices' => $invoices,
                'stats' => [
                    'total_sum_all' => round($totalAllInvoices, 2),
                    'total_sum_paid' => round($totalPaidInvoices, 2),
                    'total_sum_issued' => round($totalIssuedInvoices, 2),
                    'total_sum_cancelled' => round($totalCancelledInvoices, 2),
                    'total_sum_overdue' => round($totalOverdueInvoices, 2),
                ]
            ]
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
        return $this->getInvoicePdf($id, $request->user()->id);
    }

    public function downloadInvoicePdfPublic($id, Request $request)
    {
        $customerId = $request->query('customer_id') ?? $request->header('X-Customer-ID');
        if (!$customerId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized or missing identity.'], 401);
        }

        $invoice = CustomerInvoice::with(['client', 'articles.tax', 'customer'])
            ->where('customer_id', $customerId)
            ->find($id);

        if (!$invoice) {
            return response()->json(['status' => 'error', 'message' => 'Invoice not found.'], 404);
        }

        return $this->getInvoicePdf($id, $customerId);
    }

    /**
     * Shared helper to generate Invoice PDF for both authenticated and bot routes.
     */
    private function getInvoicePdf($id, $customerId)
    {
        $invoice = CustomerInvoice::with(['client', 'articles.tax', 'articles.product', 'customer'])
            ->where('customer_id', $customerId)
            ->find($id);

        if (!$invoice) {
            abort(404, 'Invoice not found.');
        }

        $company = $invoice->customer;

        $totals = [
            'total_ht' => $invoice->articles->sum('total_price_ht'),
            'discount' => $invoice->articles->sum('discount'),
            'afterDiscount' => $invoice->articles->sum(function ($a) {
                return $a->total_price_ht - ($a->discount ?? 0);
            }),
            'total_tva' => $invoice->articles->sum(function ($a) {
                $taxRate = $a->tax ? $a->tax->rate : 0;
                $afterDiscount = $a->total_price_ht - ($a->discount ?? 0);
                return round($afterDiscount * ($taxRate / 100), 2);
            }),
        ];
        $totals['total_ttc'] = round($totals['afterDiscount'] + $totals['total_tva'], 2);
        $totals['average_tva_percentage'] = $totals['afterDiscount'] > 0 ? round(($totals['total_tva'] / $totals['afterDiscount']) * 100, 2) : 0;

        $logoUrl = ($company && $company->avatar) ? asset('storage/' . $company->avatar) : null;
        $signatureUrl = ($company && $company->signature) ? asset('storage/' . $company->signature) : null;
        $pdfColor = $company && $company->company_color ? $company->company_color : '#4FA3D1';

        $user = Customer::find($customerId);

        $is_logo = $user?->mobilePlan?->logo ?? 0;

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
            'is_logo'          => $is_logo,
            'signature_data_uri' => $signatureDataUri,
            'pdfColor'         => $pdfColor
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

        $filename = 'Invoice_' . \Auth::user()->invoiceNumberFormatNew($invoice->invoice_number) . '.pdf';
        return $pdf->download($filename);
    }

    public function viewSingleInvoice(Request $request, $id)
    {
        $user = $request->user();

        $invoice = CustomerInvoice::where('id', $id)
            ->where('customer_id', $user->id)
            ->with(['client:id,client_name', 'articles', 'articles.tax:id,rate,name'])
            ->first();

        if (! $invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found or does not belong to the customer.'
            ], 404);
        }

        $totals = [
            'total_ht' => 0,
            'total_discount' => 0,
            'total_tva' => 0,
            'total_ttc' => 0,
        ];

        foreach ($invoice->articles as $article) {
            $priceHt = $article->unit_price_ht * ($article->quantity ?? 1);
            $discount = $article->discount ?? 0;
            $priceAfterDiscount = $priceHt - $discount;

            $taxRate = $article->tax ? $article->tax->rate : 0;
            $taxAmount = round($priceAfterDiscount * $taxRate / 100, 2);
            $totalTtc = $priceAfterDiscount + $taxAmount;

            // ✅ attach per-article totals
            $article->total_ht = $priceHt;
            $article->tax_amount = $taxAmount;
            $article->total_ttc = $totalTtc;

            // existing global totals
            $totals['total_ht'] += $priceHt;
            $totals['total_discount'] += $discount;
            $totals['total_tva'] += $taxAmount;
            $totals['total_ttc'] += $totalTtc;
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice retrieved successfully.',
            'data'    => $invoice,
            'totals'  => $totals
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
            'due_date'       => 'sometimes|required|date|after:date',
            'payment_method' => 'sometimes|required|string|max:255',
            'status'         => 'sometimes|required|string|max:50',
            'notes'          => 'nullable|string',
            'document'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20500',
            'remove_document' => 'nullable|boolean',

            // Articles validation (optional during update)
            'articles'                 => 'sometimes|array',
            'articles.*.product_id'    => 'required_with:articles|integer',
            'articles.*.designation'    => 'required_with:articles|string|max:255',
            'articles.*.unit_price_ht' => 'required_with:articles|numeric|min:0',
            'articles.*.quantity'      => 'required_with:articles|integer|min:1',
            'articles.*.total_price_ht' => 'required_with:articles|numeric|min:0',
            'articles.*.tva_percentage' => 'required_with:articles|exists:taxes,id',
            'articles.*.discount'      => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated, $invoice, $user) {

                if ($request->remove_document) {
                    if ($invoice->document_path) {
                        $user->is_b2c
                            ? Utility::changeB2CStorageLimitNew($user->id, $invoice->document_path)
                            : Utility::changeStorageLimitNew($user->companyId(), $invoice->document_path);
                        Storage::disk('private')->delete($invoice->document_path);
                    }
                    $validated['document_path'] = null;
                }

                // 1. Handle File Upload (and delete old file if a new one is uploaded)
                if ($request->hasFile('document')) {

                    $image_size = $request->file('document')->getSize();

                    $result = Utility::updateB2CStorageLimit($user->id, $image_size);

                    if ($result != 1) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Storage limit exceeded. Cannot upload document.'
                        ], 400);
                    }

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
            return DB::transaction(function () use ($invoice, $user) {
                // 1. Delete the physical file from storage if it exists
                if ($invoice->document_path) {
                    $file_path = $invoice->document_path;
                    $user->is_b2c
                        ? Utility::changeB2CStorageLimitNew($user->id, $file_path)
                        : Utility::changeStorageLimitNew($user->companyId(), $file_path);
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
            ->with(['client:id,client_name', 'articles.tax'])
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
            fputcsv($file, ['Invoice#', 'Date', 'Client', 'Status', 'Article', 'Amount TTC', 'TVA', 'Payment Method', 'Category', 'Discount', 'Total TTC', 'Total TVA']);

            foreach ($invoices as $invoice) {
                $clientName = $invoice->client->client_name ?? 'N/A';
                foreach ($invoice->articles as $article) {
                    $taxRate = $article->tax ? $article->tax->rate : 0;
                    fputcsv($file, [
                        \Auth::user()->invoiceNumberFormatNew($invoice->invoice_number),
                        $invoice->date,
                        $clientName,
                        $invoice->status,
                        $article->designation ?? '',
                        $article->total_price_ht,
                        $taxRate,
                        $invoice->payment_method,
                        $article->designation ?? '',
                        $article->discount ?? 0,
                        $article->total_price_ht,
                        $taxRate
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function duplicateInvoice(Request $request, $id)
    {
        $user = $request->user();

        $invoice = CustomerInvoice::where('id', $id)
            ->where('customer_id', $user->id)
            ->with('articles')
            ->first();

        if (! $invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found or does not belong to the customer.'
            ], 404);
        }

        try {
            return DB::transaction(function () use ($invoice) {
                $duplicateInvoice = $invoice->replicate();
                $duplicateInvoice->invoice_number = $this->invoiceNumber();

                // ✅ Handle document duplication
                if ($invoice->document_path && Storage::disk('private')->exists($invoice->document_path)) {

                    $originalPath = $invoice->document_path;

                    $extension = pathinfo($originalPath, PATHINFO_EXTENSION);

                    $newFileName = 'customer_invoices/' . Str::uuid() . '.' . $extension;

                    Storage::disk('private')->copy($originalPath, $newFileName);

                    $duplicateInvoice->document_path = $newFileName;
                }

                $duplicateInvoice->save();

                foreach ($invoice->articles as $article) {
                    $duplicateArticle = $article->replicate();
                    $duplicateArticle->invoice_id = $duplicateInvoice->id;
                    $duplicateArticle->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice duplicated successfully.',
                    'data'    => $duplicateInvoice->load('articles')
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    function invoiceNumber()
    {
        $latest = CustomerInvoice::where('customer_id', '=', auth()->id())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->invoice_number + 1;
    }

    function quoteNumber()
    {
        $latest = CustomerQuote::where('customer_id', '=', auth()->id())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->quote_number + 1;
    }

    function parseNumber($formattedNumber)
    {
        if (preg_match('/(\d+)$/', $formattedNumber, $matches)) {
            return (int)$matches[1];
        }

        return 0;
    }


    public function storeCustomerProduct(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'designation' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_services', 'name')->where(function ($query) use ($request) {
                    return $query->where('customer_id', $request->customer_id);
                }),
            ],
            'unit_price_ht' => 'required|numeric|min:0',
            'tva_percentage' => 'required|exists:taxes,id',
            'unit_id' => 'required|exists:product_service_units,id',
            'reference' => 'required|string|max:255',
            'quantity' => 'nullable|integer',
            'description' => 'nullable|string|max:255',
        ], [
            'designation.unique' => 'This product designation already exists for this customer.',
        ]);

        $validated['customer_id'] = $validated['customer_id'];

        $company_id = auth()->user()->companyId();

        $SaleCode = $request->type === 'Service' ? '71243' : '7111';
        $ExpenseCode = $request->type === 'Service' ? '61263' : '6111';

        $sale_chartaccount_id = ChartOfAccount::join('chart_of_account_types as t', 't.id', '=', 'chart_of_accounts.type')
            ->where('chart_of_accounts.created_by', $company_id)
            ->where('chart_of_accounts.code', $SaleCode)
            ->where('t.name', 'Income')
            ->value('chart_of_accounts.id');


        $expense_chartaccount_id = ChartOfAccount::join('chart_of_account_types as t', 't.id', '=', 'chart_of_accounts.type')
            ->where('chart_of_accounts.created_by', $company_id)
            ->where('chart_of_accounts.code', $ExpenseCode)
            ->whereIn('t.name', ['Expenses', 'Costs of Goods Sold'])
            ->value('chart_of_accounts.id');

        $randomNumber = Str::random(6);

        $category_id = ProductServiceCategory::where('created_by', $company_id)->where('type', 'product & service')->value('id') ?? 1;

        $productService = new ProductService();
        $productService->name           = $request->designation;
        $productService->description    = $request->description;
        $productService->sku            = $request->reference ?? 'REF-' . $randomNumber;
        $productService->sale_price     = $request->unit_price_ht;
        $productService->purchase_price = $request->unit_price_ht;
        $productService->tax_id         = $request->tva_percentage;
        $productService->quantity       = $request->quantity ?? 1;
        $productService->type           = $request->type;
        $productService->sale_chartaccount_id       = $sale_chartaccount_id ?? '1';
        $productService->expense_chartaccount_id    = $expense_chartaccount_id ?? '1';
        $productService->customer_id     = $request->customer_id;
        $productService->unit_id        = $request->unit_id;
        $productService->category_id    = $category_id;
        $productService->created_by     = $company_id;
        $productService->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer product created successfully.',
            'data'    => $productService,
            'product_id' => $productService->id
        ], 201);
    }


    public function getCustomerProducts(Request $request)
    {
        $user = $request->user();
        $like = $request->query('like');

        $products = ProductService::where('customer_id', $user->id)
            ->when($like, function ($query, $like) {
                return $query->where('name', 'like', "%{$like}%");
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

        $product = ProductService::where('id', $id)
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

        // 1. Fetch the product and verify ownership in one go
        $product = ProductService::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Customer product not found or does not belong to you.'
            ], 404);
        }

        // 2. Validate the input
        $validated = $request->validate([
            'designation'   => 'sometimes|required|string|max:255',
            'unit_price_ht' => 'sometimes|required|numeric|min:0',
            'tva_percentage'   => 'sometimes|required|exists:taxes,id',
            'quantity'      => 'nullable|integer|min:1',
            'description'   => 'nullable|string|max:255',
            'reference'     => 'nullable|string|max:255',
            'category'      => 'nullable|string|max:255',
            'type'          => 'nullable|string|max:255|in:Product,Service',
            'customer_id'   => 'sometimes|required|exists:customers,id',
            'unit_id'       => 'sometimes|required|exists:product_service_units,id',
            'category_id'   => 'sometimes|required|exists:product_service_categories,id',
        ]);

        $company_id = auth()->user()->companyId();

        $SaleName = $request->type === 'Service' ? 'Service Income' : 'Sales Income';
        $Expensename = $request->type === 'Service' ? 'Cost of Sales- On Services' : 'Cost of Sales - Purchases';

        $sale_chartaccount_id = ChartOfAccount::join('chart_of_account_types as t', 't.id', '=', 'chart_of_accounts.type')
            ->where('chart_of_accounts.created_by', $company_id)
            ->where('chart_of_accounts.name', $SaleName)
            ->where('t.name', 'Income')
            ->value('chart_of_accounts.id');


        $expense_chartaccount_id = ChartOfAccount::join('chart_of_account_types as t', 't.id', '=', 'chart_of_accounts.type')
            ->where('chart_of_accounts.created_by', $company_id)
            ->where('chart_of_accounts.name', $Expensename)
            ->whereIn('t.name', ['Expenses', 'Costs of Goods Sold'])
            ->value('chart_of_accounts.id');

        // 3. Apply updates only for provided fields
        if ($request->has('designation'))   $product->name = $validated['designation'];
        if ($request->has('description'))   $product->description = $validated['description'];
        if ($request->has('reference'))     $product->sku = $validated['reference'];
        if ($request->has('unit_price_ht')) {
            $product->sale_price = $validated['unit_price_ht'];
            $product->purchase_price = $validated['unit_price_ht'];
        }
        if ($request->has('tva_percentage'))   $product->tax_id = $validated['tva_percentage'];
        if ($request->has('quantity'))      $product->quantity = $validated['quantity'];
        if ($request->has('type'))      $product->type = $validated['type'];
        if ($request->has('customer_id'))      $product->customer_id = $validated['customer_id'];
        if ($request->has('unit_id'))      $product->unit_id = $validated['unit_id'];
        if ($request->has('category_id'))      $product->category_id = $validated['category_id'];

        if ($request->has('type')) {
            $product->sale_chartaccount_id    = $sale_chartaccount_id ?? '1';
            $product->expense_chartaccount_id = $expense_chartaccount_id ?? '1';
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer product updated successfully.',
            'data'    => $product
        ], 200);
    }


    public function deleteCustomerProduct(Request $request, $id)
    {
        $user = $request->user();

        $product = ProductService::where('id', $id)
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


    public function getQuotes(Request $request)
    {
        $user = $request->user();

        // Fetch filters from request
        $month = $request->query('month');
        $year = $request->query('year');
        $status = $request->query('status');
        $clientId = $request->query('client_id');
        $id = $request->query('id');
        $today = now()->startOfDay();

        $query = CustomerQuote::where('customer_id', $user->id)
            ->with(['client:id,client_name', 'articles', 'articles.tax:id,rate,name'])
            ->orderBy('date', 'desc');

        // --- Apply Filters (Same logic as Invoices) ---
        if ($id) {
            $query->where('id', $id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

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

        $quotes = $query->latest()->get();

        // --- Initialize Totals ---
        $totalAllQuotes = 0;
        $totalAcceptedQuotes = 0;
        $totalSentQuotes = 0;
        $totalOverdueQuotes = 0;

        // Calculate total_ttc per quote and update aggregates
        $quotes->each(function ($quote) use (&$totalAllQuotes, &$totalAcceptedQuotes, &$totalSentQuotes, &$totalOverdueQuotes, $today) {
            $totalTtc = 0;

            foreach ($quote->articles as $article) {
                $priceHt = $article->unit_price_ht * ($article->quantity ?? 1);
                $discount = $article->discount ?? 0;
                $priceAfterDiscount = $priceHt - $discount;

                $taxRate = $article->tax ? $article->tax->rate : 0;
                $taxAmount = round($priceAfterDiscount * $taxRate / 100, 2);

                $totalTtc += ($priceAfterDiscount + $taxAmount);
            }

            // Attach total to the individual quote
            $quote->total_ttc = $totalTtc;

            // --- Aggregation Logic ---
            $totalAllQuotes += $totalTtc;

            if ($quote->status === 'accepted') {
                $totalAcceptedQuotes += $totalTtc;
            }

            if ($quote->status === 'sent') {
                $totalSentQuotes += $totalTtc;
            }

            if ($quote->status === 'sent' && $quote->due_date && Carbon::parse($quote->due_date)->lt($today)) {
                $totalOverdueQuotes += $totalTtc;
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Customer quotes retrieved successfully.',
            'data'    => [
                'quotes' => $quotes,
                'stats'  => [
                    'total_sum_all'      => round($totalAllQuotes, 2),
                    'total_sum_accepted' => round($totalAcceptedQuotes, 2),
                    'total_sum_sent'     => round($totalSentQuotes, 2),
                    'total_sum_overdue'  => round($totalOverdueQuotes, 2),
                ]
            ]
        ], 200);
    }


    public function storeQuote(Request $request)
    {

        // ✅ STEP 1: Validation
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'client_id'   => 'required|exists:customer_clients,id',
            'date'        => 'required|date',
            'due_date'    => 'required|date|after:date',
            'quote_number' => [
                'required',
                'string',
                'max:255',
            ],
            'payment_method' => 'required|string|max:255',
            'status'      => 'required|string|max:50',
            'notes'       => 'nullable|string',
            'document'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20500',

            'articles'                    => 'sometimes|array',
            'articles.*.designation'     => 'required_with:articles|string|max:255',
            'articles.*.product_id'      => 'nullable|integer',
            'articles.*.unit_price_ht'   => 'required_with:articles|numeric|min:0',
            'articles.*.quantity'        => 'nullable|integer|min:1',
            'articles.*.total_price_ht'  => 'nullable|numeric|min:0',
            'articles.*.tva_percentage'  => 'required_with:articles|exists:taxes,id',
            'articles.*.discount'        => 'nullable|numeric|min:0|max:100',
        ]);

        if (!empty($request->document)) {
            $image_size = $request->file('document')->getSize();
            $user = $request->user();

            $result = Utility::updateB2CStorageLimit($user->id, $image_size);

            if ($result != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Storage limit exceeded. Cannot upload document.'
                ], 400);
            }
        }

        try {
            // ✅ STEP 2: Prepare Data
            $quoteData = collect($validated)->except(['articles', 'document'])->toArray();
            $quoteData['quote_number'] = $this->parseNumber($validated['quote_number']);

            $articlesData = [];

            if (!empty($validated['articles'])) {
                foreach ($validated['articles'] as $article) {
                    $quantity = $article['quantity'] ?? 1;

                    $articlesData[] = [
                        'product_id'     => $article['product_id'] ?? 1,
                        'designation'    => $article['designation'],
                        'unit_price_ht'  => $article['unit_price_ht'],
                        'quantity'       => $quantity,
                        'total_price_ht' => $article['total_price_ht'] ?? ($article['unit_price_ht'] * $quantity),
                        'tva_percentage' => $article['tva_percentage'],
                        'discount'       => $article['discount'] ?? 0,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }
            }


            // ✅ STEP 3: DB Transaction (FAST)
            $quote = DB::transaction(function () use ($quoteData, $articlesData) {

                $quote = CustomerQuote::create($quoteData);

                if (!empty($articlesData)) {
                    foreach ($articlesData as &$article) {
                        $article['quotes_id'] = $quote->id;
                    }

                    // 🚀 BULK INSERT (single query instead of many)
                    DB::table('quotes_articles')->insert($articlesData);
                }

                return $quote;
            });


            // ✅ STEP 4: File Upload AFTER DB (non-blocking DB)
            if ($request->hasFile('document')) {

                $documentPath = $request->file('document')->store('customer_quotes', 'private');

                $quote->update([
                    'document_path' => $documentPath
                ]);
            }


            // ✅ STEP 5: Response (no extra DB query)
            return response()->json([
                'success' => true,
                'message' => 'Quote created successfully.',
                'data'    => $quote->setRelation(
                    'articles',
                    collect($validated['articles'] ?? [])
                )
            ], 201);
        } catch (\Exception $e) {


            return response()->json([
                'success' => false,
                'message' => 'Failed to create quote: ' . $e->getMessage()
            ], 500);
        }
    }


    public function viewSingleQuote(Request $request, $id)
    {
        $user = $request->user();

        $quote = CustomerQuote::where('id', $id)
            ->where('customer_id', $user->id)
            ->with(['client:id,client_name', 'articles', 'articles.tax:id,rate,name'])
            ->first();

        if (! $quote) {
            return response()->json([
                'success' => false,
                'message' => 'Quote not found or does not belong to the customer.'
            ], 404);
        }


        $totals = [
            'total_ht' => 0,
            'total_discount' => 0,
            'total_tva' => 0,
            'total_ttc' => 0,
        ];

        foreach ($quote->articles as $article) {
            $priceHt = $article->unit_price_ht * ($article->quantity ?? 1);
            $discount = $article->discount ?? 0;
            $priceAfterDiscount = $priceHt - $discount;

            $taxRate = $article->tax ? $article->tax->rate : 0;
            $taxAmount = round($priceAfterDiscount * $taxRate / 100, 2);
            $totalTtc = $priceAfterDiscount + $taxAmount;

            // ✅ attach per-article totals
            $article->total_ht = $priceHt;
            $article->tax_amount = $taxAmount;
            $article->total_ttc = $totalTtc;


            $totals['total_ht'] += $priceHt;
            $totals['total_discount'] += $discount;
            $totals['total_tva'] += $taxAmount;
            $totals['total_ttc'] += $totalTtc;
        }

        return response()->json([
            'success' => true,
            'message' => 'Quote retrieved successfully.',
            'data'    => $quote,
            'totals'  => $totals
        ], 200);
    }


    public function updateQuote(Request $request, $id)
    {
        $user = $request->user();

        $quote = CustomerQuote::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (!$quote) {
            return response()->json([
                'success' => false,
                'message' => 'Quote not found or does not belong to the customer.'
            ], 404);
        }

        $validated = $request->validate([
            'client_id'      => 'sometimes|required|exists:customer_clients,id',
            'date'           => 'sometimes|required|date',
            'due_date'       => 'sometimes|required|date|after:date',
            'payment_method' => 'sometimes|required|string|max:255',
            'status'         => 'sometimes|required|string|max:50',
            'review_status'  => 'sometimes|required|string|max:50',
            'notes'          => 'nullable|string',
            'document'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20500',
            'remove_document' => 'nullable|boolean',

            // Articles validation
            'articles'                 => 'sometimes|array',
            'articles.*.product_id'    => 'required_with:articles|integer',
            'articles.*.designation'    => 'required_with:articles|string|max:255',
            'articles.*.unit_price_ht' => 'required_with:articles|numeric|min:0',
            'articles.*.quantity'      => 'required_with:articles|integer|min:1',
            'articles.*.total_price_ht' => 'required_with:articles|numeric|min:0',
            'articles.*.tva_percentage' => 'required_with:articles|exists:taxes,id',
            'articles.*.discount'      => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated, $quote, $user) {

                if ($request->remove_document) {
                    if ($quote->document_path) {
                        $user->is_b2c
                            ? Utility::changeB2CStorageLimitNew($user->id, $quote->document_path)
                            : Utility::changeStorageLimitNew($user->companyId(), $quote->document_path);
                        Storage::disk('private')->delete($quote->document_path);
                    }
                    $validated['document_path'] = null;
                }

                // 1. Handle File Upload
                if ($request->hasFile('document')) {

                    $image_size = $request->file('document')->getSize();

                    $result = Utility::updateB2CStorageLimit($user->id, $image_size);

                    if ($result != 1) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Storage limit exceeded. Cannot upload document.'
                        ], 400);
                    }
                    if ($quote->document_path) {
                        Storage::disk('private')->delete($quote->document_path);
                    }
                    $path = $request->file('document')->store('customer_quotes', 'private');
                    $validated['document_path'] = $path;
                }

                // 2. Update Quote Header
                $quote->update($validated);

                if ($request->has('articles')) {
                    // Delete existing articles first
                    $quote->articles()->delete();

                    // If the array isn't empty, create the new ones
                    if (!empty($validated['articles'])) {
                        // We need to map the articles to include the quote_id
                        $articlesData = array_map(function ($article) use ($quote) {
                            return array_merge($article, ['quotes_id' => $quote->id]);
                        }, $validated['articles']);

                        $quote->articles()->createMany($articlesData);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Quote and articles updated successfully.',
                    'data'    => $quote->load('articles')
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteQuote(Request $request, $id)
    {
        $user = $request->user();

        $quote = CustomerQuote::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (!$quote) {
            return response()->json([
                'success' => false,
                'message' => 'Quote not found or does not belong to the customer.'
            ], 404);
        }

        try {
            return DB::transaction(function () use ($quote, $user) {
                // 1. Delete the physical file from storage if it exists
                if ($quote->document_path) {
                    $file_path = $quote->document_path;
                    $user->is_b2c
                        ? Utility::changeB2CStorageLimitNew($user->id, $file_path)
                        : Utility::changeStorageLimitNew($user->companyId(), $file_path);
                    Storage::disk('private')->delete($quote->document_path);
                }

                // Delete associated articles first
                $quote->articles()->delete();
                $quote->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Quote and associated files deleted successfully.'
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete quote: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportQuotes(Request $request)
    {
        $user = $request->user();
        $quotes = CustomerQuote::where('customer_id', $user->id)
            // ->where(function ($q) {
            //     $q->where('review_status', '!=', 'CONVERTED')
            //         ->orWhereNull('review_status');
            // })
            ->with(['client:id,client_name', 'articles.tax'])
            ->orderBy('date', 'desc')
            ->get();

        $fileName = "quotes_" . now()->format('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($quotes) {
            $file = fopen('php://output', 'w');

            // Add Headers
            fputcsv($file, ['Quote#', 'Date', 'Client', 'Status', 'Article', 'Amount TTC', 'TVA', 'Payment Method', 'Category', 'Discount', 'Total TTC', 'Total TVA']);

            foreach ($quotes as $quote) {
                $clientName = $quote->client->client_name ?? 'N/A';
                foreach ($quote->articles as $article) {
                    $taxRate = $article->tax ? $article->tax->rate : 0;
                    fputcsv($file, [
                        \Auth::user()->quoteNumberFormatNew($quote->quote_number),
                        $quote->date,
                        $clientName,
                        $quote->status,
                        $article->designation ?? '',
                        $article->total_price_ht,
                        $taxRate,
                        $quote->payment_method,
                        $article->designation ?? '',
                        $article->discount ?? 0,
                        $article->total_price_ht,
                        $taxRate
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadQuote(Request $request, $id)
    {
        $quote = CustomerQuote::where('customer_id', $request->user()->id)
            ->findOrFail($id);

        if (!$quote->document_path || !Storage::disk('private')->exists($quote->document_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Quote file not found on server.'
            ], 404);
        }

        return Storage::disk('private')->download($quote->document_path, 'Quote_' . $quote->id . '.' . pathinfo($quote->document_path, PATHINFO_EXTENSION));
    }

    public function downloadQuotePdf(Request $request, $id)
    {
        $user = $request->user();

        $quote = CustomerQuote::where('customer_id', $request->user()->id)
            ->with(['client', 'articles.tax', 'customer'])
            ->findOrFail($id);

        $company = $quote->customer;

        $totals = [
            'total_ht' => $quote->articles->sum('total_price_ht'),
            'discount' => $quote->articles->sum('discount'),
            'afterDiscount' => $quote->articles->sum(function ($a) {
                return $a->total_price_ht - ($a->discount ?? 0);
            }),
            'total_tva' => $quote->articles->sum(function ($a) {
                $taxRate = $a->tax ? $a->tax->rate : 0;
                $afterDiscount = $a->total_price_ht - ($a->discount ?? 0);
                return round($afterDiscount * ($taxRate / 100), 2);
            }),
        ];
        $totals['total_ttc'] = round($totals['afterDiscount'] + $totals['total_tva'], 2);
        $totals['average_tva_percentage'] = $totals['afterDiscount'] > 0 ? round(($totals['total_tva'] / $totals['afterDiscount']) * 100, 2) : 0;

        $logoUrl = ($company && $company->avatar) ? asset('storage/' . $company->avatar) : null;
        $signatureUrl = ($company && $company->signature) ? asset('storage/' . $company->signature) : null;
        $pdfColor = $company && $company->company_color ? $company->company_color : '#4FA3D1';

        $is_logo = $request->user()?->mobilePlan?->logo ?? 0;

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
            'invoice'          => $quote,
            'company'          => $company,
            'totals'           => $totals,
            'currency_symbol'  => $company ? $company->currencySymbol() : '',
            'logo_url'         => $logoUrl,
            'signature_url'    => $signatureUrl,
            'logo_data_uri'    => $logoDataUri,
            'is_logo'          => $is_logo,
            'signature_data_uri' => $signatureDataUri,
            'pdfColor'         => $pdfColor
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

        $filename = 'Quote_' . \Auth::user()->quoteNumberFormatNew($quote->quote_number) . '.pdf';
        return $pdf->download($filename);
    }


    public function getQuoteStatusChart(Request $request)
    {
        $user  = $request->user();
        $month = $request->query('month');
        $year  = $request->query('year');

        $query = CustomerQuote::where('customer_id', $user->id)
            // ->where(function ($q) {
            //     $q->where('review_status', '!=', 'CONVERTED')
            //         ->orWhereNull('review_status');
            // })
            ->select(
                'status as label',
                DB::raw('COUNT(*) as value')
            );

        // Date filters (same logic as your previous API)
        if ($year && $month) {
            $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end   = $start->copy()->endOfMonth();

            $query->whereBetween('date', [$start, $end]);
        } elseif ($year) {
            $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $end   = $start->copy()->endOfYear();

            $query->whereBetween('date', [$start, $end]);
        } elseif ($month) {
            $query->whereMonth('date', $month)
                ->whereYear('date', now()->year);
        }

        $rows = $query->groupBy('status')
            ->orderByDesc('value')
            ->get();

        $convertedQuotes = CustomerQuote::where('customer_id', $user->id)->where('review_status', 'CONVERTED')->count();

        return response()->json([
            'success' => true,
            'message' => 'Quote totals by status retrieved successfully.',
            'data'    => $rows,
            'convertedQuotes' => $convertedQuotes,

            // Optional (frontend friendly)
            'labels'  => $rows->pluck('label'),
            'values'  => $rows->pluck('value'),
        ]);
    }

    public function duplicateQuote(Request $request, $id)
    {
        $user = $request->user();

        $quote = CustomerQuote::where('id', $id)
            ->where('customer_id', $user->id)
            ->with('articles')
            ->first();

        if (! $quote) {
            return response()->json([
                'success' => false,
                'message' => 'Quote not found or does not belong to the customer.'
            ], 404);
        }

        try {
            return DB::transaction(function () use ($quote) {
                $duplicateQuote = $quote->replicate();
                $duplicateQuote->quote_number = $this->quoteNumber();

                // ✅ Handle document duplication
                if ($quote->document_path && Storage::disk('private')->exists($quote->document_path)) {

                    $originalPath = $quote->document_path;

                    $extension = pathinfo($originalPath, PATHINFO_EXTENSION);

                    $newFileName = 'customer_quotes/' . Str::uuid() . '.' . $extension;

                    Storage::disk('private')->copy($originalPath, $newFileName);

                    $duplicateQuote->document_path = $newFileName;
                }

                $duplicateQuote->save();

                foreach ($quote->articles as $article) {
                    $duplicateArticle = $article->replicate();
                    $duplicateArticle->quotes_id = $duplicateQuote->id;
                    $duplicateArticle->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Quote duplicated successfully.',
                    'data'    => $duplicateQuote->load('articles')
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate quote: ' . $e->getMessage()
            ], 500);
        }
    }


    public function quoteToInvoice(Request $request, $id)
    {
        try {
            return \DB::transaction(function () use ($request, $id) {
                $quote = CustomerQuote::where('id', $id)
                    ->where('customer_id', $request->user()->id)
                    ->first();

                if (!$quote) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quote not found or does not belong to the customer.'
                    ], 404);
                }

                if ($quote->status != 'accepted') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quote is not accepted yet.'
                    ], 404);
                }

                $articles = QuoteArticle::where('quotes_id', $quote->id)->get();

                $invoice = new CustomerInvoice();
                $invoice->customer_id = $quote->customer_id;
                $invoice->client_id = $quote->client_id;
                $invoice->date = $quote->date;
                $invoice->due_date = $quote->due_date;
                $invoice->invoice_number = $this->invoiceNumber();
                $invoice->payment_method = $quote->payment_method;
                $invoice->status = 'issued';
                $invoice->review_status = 'PENDING';
                $invoice->notes = $quote->notes;
                $invoice->document_path = $quote->document_path;
                $invoice->save();

                if ($articles->count() > 0) {
                    $articlesData = $articles->map(function ($item) {
                        return [
                            'product_id'     => $item->product_id,
                            'designation'    => $item->designation,
                            'unit_price_ht'  => $item->unit_price_ht,
                            'quantity'       => $item->quantity,
                            'total_price_ht' => $item->total_price_ht,
                            'tva_percentage' => $item->tva_percentage,
                        ];
                    })->toArray();

                    $invoice->articles()->createMany($articlesData);
                }

                $quote->review_status = 'CONVERTED';
                $quote->save();
                // QuoteArticle::where('quotes_id', $quote->id)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Quote converted to invoice successfully.',
                    'data'    => $invoice->load('articles')
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to convert quote to invoice: ' . $e->getMessage()
            ], 500);
        }
    }


    public function storeRevenue(Request $request)
    {

        $validated = $request->validate([
            'date'           => 'required|date',
            'amount'         => 'required|numeric|min:0',
            'account_id'     => 'nullable|exists:bank_accounts,id',
            'category_id'    => 'nullable|exists:categories,id',
            'reference'      => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'payment_method' => 'nullable|string',
            'add_receipt'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20500',
            'is_ocr'         => 'nullable|boolean',
        ]);

        try {
            $revenueData = collect($validated)->toArray();
            $revenueData['add_receipt'] = null;

            if ($request->hasFile('add_receipt')) {

                $image_size = $request->file('add_receipt')->getSize();

                $user = $request->user();

                $result = Utility::updateB2CStorageLimit($user->id, $image_size);

                if ($result == 1) {
                    $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                    $revenueData['add_receipt'] = $fileName;
                    $dir        = 'uploads/revenue';
                    $path = Utility::upload_file($request, 'add_receipt', $fileName, $dir, []);

                    if ($path['flag'] == 0) {
                        return response()->json([
                            'success' => false,
                            'message' => __($path['msg'])
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $result
                    ], 400);
                }
            }

            $categoryID = ProductServiceCategory::where('created_by', auth()->user()->companyId())->where('type', 'income')->first();

            if ($request->payment_method == 'cash') {
                $bankAccountID = BankAccount::where('customer_id', auth()->user()->id)->where('bank_name', 'like', '%Caisse%')->latest()->first();
            } else {
                $bankAccountID = BankAccount::where('customer_id', auth()->user()->id)->where('bank_name', 'like', '%Banque principale%')->latest()->first();
            }


            $revenueData['account_id'] = $bankAccountID ? $bankAccountID->id : null;
            $revenueData['category_id'] = $categoryID ? $categoryID->id : null;
            $revenueData['customer_id'] = $request->user()->id;
            $revenueData['created_by'] = \Auth::user()->companyId();
            $revenue = Revenue::create($revenueData);


            $category            = ProductServiceCategory::where('id', $categoryID->id)->first();
            $revenue->payment_id = $revenue->id;
            $revenue->type       = 'Revenue';
            $revenue->category   = $category->name;
            $revenue->user_id    = $revenue->customer_id;
            $revenue->user_type  = 'Customer';
            $revenue->account    = $bankAccountID->id;
            Transaction::addTransaction($revenue);


            $customer         = Customer::where('id', $request->customer_id)->first();
            $payment          = new InvoicePayment();
            $payment->name    = !empty($customer) ? $customer['name'] : '';
            $payment->date    = \Auth::user()->dateFormat($request->date);
            $payment->amount  = \Auth::user()->priceFormat($request->amount);
            $payment->invoice = '';

            if (!empty($customer)) {
                Utility::userBalance('customer', $customer->id, $revenue->amount, 'debit');
            }

            Utility::bankAccountBalance($bankAccountID->id, $revenue->amount, 'credit');

            $accountId = BankAccount::find($revenue->account_id);
            $data = [
                'account_id' => $accountId->chart_account_id,
                'transaction_type' => 'Credit',
                'transaction_amount' => $revenue->amount,
                'reference' => 'Revenue',
                'reference_id' => $revenue->id,
                'reference_sub_id' => 0,
                'date' => $revenue->date,
            ];
            Utility::addTransactionLines(array_merge($data, ['isApp' => 1]));

            $uArr = [
                'payment_name' => $payment->name,
                'payment_amount' => $payment->amount,
                'invoice_number' => $revenue->type,
                'payment_date' => $payment->date,
                'payment_dueAmount' => '-',

            ];
            try {
                $resp = Utility::sendEmailTemplate('new_invoice_payment', [$customer->id => $customer->email], $uArr);
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            // Twilio Notification
            $setting  = Utility::settings(\Auth::user()->companyId());
            $customer = Customer::find($request->customer_id);
            if (isset($setting['revenue_notification']) && $setting['revenue_notification'] == 1) {
                $uArr = [
                    'payment_name' => $payment->name,
                    'payment_amount' => $payment->amount,
                    'payment_date' => $payment->date,
                    'user_name' => \Auth::user()->name,

                ];
                Utility::send_twilio_msg($customer->contact, 'new_revenue', $uArr);
            }

            // webhook
            $module = 'New Revenue';
            $webhook =  Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($revenue);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    return redirect()->route('revenue.index')->with('success', __('Revenue successfully created.'));
                } else {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Revenue entry created successfully.',
                'data'    => $revenue
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create revenue entry: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getRevenues(Request $request)
    {
        $user = $request->user();

        $revenues = Revenue::where('customer_id', $user->id)
            ->with(['category:id,name', 'account:id,bank_name'])
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Revenue entries retrieved successfully.',
            'data'    => $revenues
        ], 200);
    }

    public function viewSingleRevenue(Request $request, $id)
    {
        $user = $request->user();

        $revenue = Revenue::where('id', $id)
            ->where('customer_id', $user->id)
            ->with(['category:id,name', 'account:id,bank_name'])
            ->first();

        if (! $revenue) {
            return response()->json([
                'success' => false,
                'message' => 'Revenue entry not found or does not belong to the customer.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Revenue entry retrieved successfully.',
            'data'    => $revenue
        ], 200);
    }

    public function updateRevenue(Request $request, $id)
    {
        $user = $request->user();

        $revenue = Revenue::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (! $revenue) {
            return response()->json([
                'success' => false,
                'message' => 'Revenue entry not found or does not belong to the customer.'
            ], 404);
        }

        $validated = $request->validate([
            'date'           => 'sometimes|required|date',
            'amount'         => 'sometimes|required|numeric|min:0',
            'account_id'     => 'nullable|exists:bank_accounts,id',
            'category_id'    => 'nullable|exists:categories,id',
            'reference'      => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'payment_method' => 'nullable|string',
            'add_receipt'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20500',
            'remove_document' => 'nullable|boolean',
        ]);

        try {
            if ($request->remove_document) {
                if ($revenue->add_receipt) {
                    $file_path = 'uploads/revenue/' . $revenue->add_receipt;
                    Utility::changeStorageLimit(\Auth::user()->companyId(), $file_path);

                    $path = storage_path('uploads/revenue/' . $revenue->add_receipt);
                    if (file_exists($path)) {
                        \File::delete($path);
                    }
                }

                $validated['add_receipt'] = null;
            }

            if ($request->hasFile('add_receipt')) {
                $image_size = $request->file('add_receipt')->getSize();

                $result = Utility::updateB2CStorageLimit($user->id, $image_size);

                if ($result == 1) {
                    // Delete old file if exists
                    if ($revenue->add_receipt) {
                        $oldFilePath = storage_path($revenue->add_receipt);
                        if (file_exists($oldFilePath)) {
                            \File::delete($oldFilePath);
                        }
                    }

                    $fileName = time() . "_" . $request->add_receipt->getClientOriginalName();
                    $validated['add_receipt'] = $fileName;

                    $dir        = 'uploads/revenue';
                    $path = Utility::upload_file($request, 'add_receipt', $fileName, $dir, []);
                    if ($path['flag'] == 0) {
                        return response()->json([
                            'success' => false,
                            'message' => __($path['msg'])
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $result
                    ], 400);
                }
            }

            if ($request->payment_method == 'cash') {
                $bankAccountID = BankAccount::where('customer_id', auth()->user()->id)->where('bank_name', 'like', '%Caisse%')->latest()->first();
            } else {
                $bankAccountID = BankAccount::where('customer_id', auth()->user()->id)->where('bank_name', 'like', '%Banque principale%')->latest()->first();
            }
            $validated['account_id'] = $bankAccountID ? $bankAccountID->id : null;

            $revenue->update($validated);



            $category            = ProductServiceCategory::where('id', $revenue->category_id)->first();
            $revenue->category   = $category->name;
            $revenue->payment_id = $revenue->id;
            $revenue->type       = 'Revenue';
            $revenue->account    = $request->account_id;
            Transaction::editTransaction($revenue);

            $accountId = BankAccount::find($revenue->account_id);
            $data = [
                'account_id' => $accountId->chart_account_id,
                'transaction_type' => 'Credit',
                'transaction_amount' => $revenue->amount,
                'reference' => 'Revenue',
                'reference_id' => $revenue->id,
                'reference_sub_id' => 0,
                'date' => $revenue->date,
            ];
            Utility::addTransactionLines($data);

            return response()->json([
                'success' => true,
                'message' => 'Revenue entry updated successfully.',
                'data'    => $revenue
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update revenue entry: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteRevenue(Request $request, $id)
    {
        $user = $request->user();

        $revenue = Revenue::where('id', $id)
            ->where('customer_id', $user->id)
            ->first();

        if (! $revenue) {
            return response()->json([
                'success' => false,
                'message' => 'Revenue entry not found or does not belong to the customer.'
            ], 404);
        }

        try {
            if ($revenue->add_receipt) {
                $filePath = storage_path($revenue->add_receipt);
                if (file_exists($filePath)) {
                    \File::delete($filePath);
                }
            }

            $revenue->delete();

            return response()->json([
                'success' => true,
                'message' => 'Revenue entry and associated receipt deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete revenue entry: ' . $e->getMessage()
            ], 500);
        }
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


    public function getBotActivationStatus(Request $request)
    {
        $customer = $request->user();

        return response()->json([
            'bot_active' => $customer->bot_active,
            'bot_verified_at' => $customer->bot_verified_at,
            'bot_contact' => $customer->contact,
        ]);
    }

    public function requestBotDeactivation(Request $request)
    {
        $customer = $request->user();
        $customer->bot_active = false;
        $customer->bot_verified_at = null;
        $customer->bot_otp = null;
        $customer->bot_otp_expires_at = null;
        $customer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'WhatsApp Bot deactivated successfully.'
        ]);
    }


    /**
     * Request Activation (OTP) for WhatsApp Bot
     */
    public function requestActivation(Request $request)
    {
        $request->validate([
            'phone' => 'required'
        ]);

        $customer = Customer::where('contact', $request->phone)->first();
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer not found. Please register on the web portal first.'], 404);
        }

        $otp = rand(1000, 9999);
        $customer->bot_otp = $otp;
        $customer->bot_otp_expires_at = now()->addMinutes(15);
        $customer->save();

        // --- NEW: Trigger WhatsApp Bot to send the OTP ---
        try {
            $botUrl = config('ai_limits.bot_url');
            $botSecret = config('ai_limits.bot_secret');

            Http::withHeaders([
                'X-Bot-Secret' => $botSecret
            ])->post("{$botUrl}/api/v1/bot/send-otp", [
                'phone' => $request->phone,
                'otp'   => (string)$otp
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to trigger WhatsApp Bot OTP: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Activation code sent to WhatsApp. Valid for 15 minutes.',
            'debug_otp' => $otp
        ]);
    }

    /**
     * Verify Activation for WhatsApp Bot
     */
    public function verifyActivation(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required'
        ]);

        $customer = Customer::where('contact', $request->phone)
            ->where('bot_otp', $request->otp)
            ->where('bot_otp_expires_at', '>', now())
            ->first();

        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired activation code.'], 400);
        }

        $customer->bot_active = true;
        $customer->bot_verified_at = now();
        $customer->bot_otp = null;
        $customer->bot_otp_expires_at = null;
        $customer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'WhatsApp Bot activated successfully!'
        ]);
    }

    /**
     * Public file download for the Bot AI (OpenAI Needs a URL)
     */
    /**
     * Public file download for the Bot AI (OpenAI Needs a URL)
     */
    public function downloadFilePublic($id, Request $request)
    {
        $customerId = auth()->id() ?? $request->query('customer_id');

        if (!$customerId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized or missing identity.'], 401);
        }

        $type = $request->query('type'); // Optional: 'invoice', 'expense', 'receipt', 'statement'

        // 1. Check Expenses (Column: file)
        if (!$type || $type === 'expense') {
            $expense = CustomerExpense::where('customer_id', $customerId)->find($id);
            if ($expense && $expense->file && Storage::disk('private')->exists($expense->file)) {
                return Storage::disk('private')->download($expense->file);
            }
        }

        // 2. Check Invoices (Column: document_path)
        if (!$type || $type === 'invoice') {
            $invoice = CustomerInvoice::where('customer_id', $customerId)->find($id);
            if ($invoice && $invoice->document_path && Storage::disk('private')->exists($invoice->document_path)) {
                return Storage::disk('private')->download($invoice->document_path);
            }
        }

        // 3. Check Receipts (Column: attachment_path, Disk: public)
        if (!$type || $type === 'receipt') {
            $transaction = ClientTransaction::where('customer_id', $customerId)->find($id);
            if ($transaction && $transaction->attachment_path && Storage::disk('public')->exists($transaction->attachment_path)) {
                return Storage::disk('public')->download($transaction->attachment_path);
            }
        }

        // 4. Check Statements (Column: file_path, Disk: private)
        if (!$type || $type === 'statement') {
            $statement = ClientBankStatement::where('customer_id', $customerId)->find($id);
            if ($statement && $statement->file_path && Storage::disk('private')->exists($statement->file_path)) {
                return Storage::disk('private')->download($statement->file_path);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'File not found.'], 404);
    }

    /**
     * Check if user is allowed to use AI (Internal Bot API)
     */
    public function aiStatus(Request $request)
    {
        $limitService = new \App\Services\AiLimitService();
        return response()->json($limitService->checkCanUseAI($request->user()->id));
    }

    /**
     * Record AI Usage (Internal Bot API)
     */
    public function aiLog(Request $request)
    {
        $request->validate([
            'model' => 'required',
            'tokens_in' => 'required|integer',
            'tokens_out' => 'required|integer',
        ]);

        $limitService = new \App\Services\AiLimitService();
        $limitService->recordUsage(
            $request->user()->id,
            $request->model,
            $request->tokens_in,
            $request->tokens_out
        );

        return response()->json(['status' => 'success']);
    }
    /**
     * Notify Accountant about a new WhatsApp Document
     */
    private function notifyAccountant($customer, $type, $amount = null, $monthYear = null)
    {
        try {
            $accountant = $customer->accountant; // BelongsTo relationship to User (created_by)

            if (!$accountant || !$accountant->email) {
                return;
            }

            $details = [
                'customer_name' => $customer->name,
                'type'          => $type,
                'amount'        => $amount ? $customer->priceFormat($amount) : null,
                'month_year'    => $monthYear,
                'date'          => now()->format('Y-m-d H:i'),
                'dashboard_url' => url('/login'), // Link to dashboard
            ];

            // Ensure SMTP is ready (using system settings)
            // Note: This is now handled within the mailable's build() for queue compatibility
            \Mail::to($accountant->email)->queue(new \App\Mail\WhatsAppDocumentNotification($details, $accountant->id));
        } catch (\Exception $e) {
            \Log::error("WhatsApp Notification Error: " . $e->getMessage());
        }
    }
}
