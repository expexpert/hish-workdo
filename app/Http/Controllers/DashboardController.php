<?php

namespace App\Http\Controllers;

use App\Models\BalanceSheet;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\Goal;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\ProductServiceCategory;
use App\Models\ProductServiceUnit;
use App\Models\Revenue;
use App\Models\Tax;
use App\Models\Utility;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;
use App\Models\ClientNotification;
use App\Models\CustomerInvoice;
use App\Models\CustomerExpense;
use App\Models\MobileUserPlan;
use App\Models\MobileUserPlanPrice;
use App\Models\MobileUserSubscription;
use App\Models\ChartOfAccount;
use App\Models\ReferralCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::check()) {
            if (\Auth::user()->type == 'super admin') {
                $user                       = \Auth::user();
                $user['total_user']         = $user->countCompany();
                $user['total_paid_user']    = $user->countPaidCompany();
                $user['total_orders']       = Order::total_orders();
                $user['total_orders_price'] = Order::total_orders_price();
                $user['total_plan']         = Plan::total_plan();
                $user['most_purchese_plan'] = (!empty(Plan::most_purchese_plan()) ? Plan::most_purchese_plan()->total : 0);
                $chartData                  = $this->getOrderChart(['duration' => 'week']);

                return view('dashboard.super_admin', compact('user', 'chartData'));
            } else {
                if (\Auth::user()->can('show dashboard')) {

                    $incomeCategory = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'income')->get();
                    $inColor        = array();
                    $inCategory     = array();
                    $inAmount       = array();
                    for ($i = 0; $i < count($incomeCategory); $i++) {
                        $inColor[]    = $incomeCategory[$i]->color;
                        $inCategory[] = $incomeCategory[$i]->name;
                        $inAmount[]   = $incomeCategory[$i]->incomeCategoryRevenueAmount();
                    }


                    $data['incomeCategoryColor'] = $inColor;
                    $data['incomeCategory']      = $inCategory;
                    $data['incomeCatAmount']     = $inAmount;

                    $expenseCategory = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'expense')->get();
                    $exColor         = array();
                    $exCategory      = array();
                    $exAmount        = array();
                    for ($i = 0; $i < count($expenseCategory); $i++) {
                        $exColor[]    = $expenseCategory[$i]->color;
                        $exCategory[] = $expenseCategory[$i]->name;
                        $exAmount[]   = $expenseCategory[$i]->expenseCategoryAmount();
                    }

                    $data['expenseCategoryColor'] = $exColor;
                    $data['expenseCategory']      = $exCategory;
                    $data['expenseCatAmount']     = $exAmount;

                    $data['incExpBarChartData']  = \Auth::user()->getincExpBarChartData();
                    $data['incExpLineChartData'] = \Auth::user()->getIncExpLineChartDate();
                    $data['accountantChartData'] = \Auth::user()->getAccountantChartData();

                    $data['currentYear']  = date('Y');
                    $data['currentMonth'] = date('M');

                    $constant['taxes']         = Tax::where('created_by', \Auth::user()->creatorId())->count();
                    $constant['category']      = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->count();
                    $constant['units']         = ProductServiceUnit::where('created_by', \Auth::user()->creatorId())->count();
                    $constant['bankAccount']   = BankAccount::where('created_by', \Auth::user()->creatorId())->count();
                    $data['constant']          = $constant;
                    $data['bankAccountDetail'] = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get();
                    $data['recentInvoice']     = Invoice::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                    $data['weeklyInvoice']     = \Auth::user()->weeklyInvoice();
                    $data['monthlyInvoice']    = \Auth::user()->monthlyInvoice();
                    $data['recentBill']        = Bill::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                    $data['weeklyBill']        = \Auth::user()->weeklyBill();
                    $data['monthlyBill']       = \Auth::user()->monthlyBill();
                    $data['goals']             = Goal::where('created_by', '=', \Auth::user()->creatorId())->where('is_display', 1)->get();
                } else {
                    $data = [];
                }

                if (\Auth::user()->type == 'accountant') {

                    $data['latestIncome']  = CustomerInvoice::whereIn('customer_id', \Auth::user()->getAccountantCustomersIds())->orderBy('id', 'desc')->limit(5)->get();
                    $data['latestExpense'] = CustomerExpense::whereIn('customer_id', \Auth::user()->getAccountantCustomersIds())->orderBy('id', 'desc')->limit(5)->get();



                    $data['totalCustomers'] = Customer::where('created_by', \Auth::user()->id)->where('is_active', 1)->count();
                    $data['totalInvoices'] = CustomerInvoice::whereIn('customer_id', \Auth::user()->getAccountantCustomersIds())->count();
                    $data['totalExpenses'] = CustomerExpense::whereIn('customer_id', \Auth::user()->getAccountantCustomersIds())->count();


                    $data['currentMonthRevenue'] = CustomerInvoice::join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
                        ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
                        ->whereIn('customer_invoices.customer_id', \Auth::user()->getAccountantCustomersIds())
                        ->whereIn('customer_invoices.status', ['issued', 'paid'])
                        ->whereMonth('customer_invoices.date', date('m'))
                        ->whereYear('customer_invoices.date', date('Y'))
                        ->selectRaw('SUM((invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0)) * (1 + COALESCE(taxes.rate, 0) / 100)) as total')
                        ->value('total');


                    $data['TodayRevenue'] = CustomerInvoice::join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
                        ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
                        ->whereIn('customer_invoices.customer_id', \Auth::user()->getAccountantCustomersIds())
                        ->whereIn('customer_invoices.status', ['issued', 'paid'])
                        ->whereDate('customer_invoices.date', date('Y-m-d'))
                        ->selectRaw('SUM((invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0)) * (1 + COALESCE(taxes.rate, 0) / 100)) as total')
                        ->value('total');

                    $data['currentMonthExpense'] = CustomerExpense::whereIn('customer_id', \Auth::user()->getAccountantCustomersIds())
                        ->whereMonth('date', date('m'))
                        ->whereYear('date', date('Y'))
                        ->sum('ttc');

                    $data['TodayExpense'] = CustomerExpense::whereIn('customer_id', \Auth::user()->getAccountantCustomersIds())
                        ->whereDate('date', date('Y-m-d'))
                        ->sum('ttc');

                    $data['netResult'] = $data['currentMonthRevenue'] - $data['currentMonthExpense'];


                    $data['totalVatCollected'] = CustomerInvoice::join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
                        ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
                        ->whereIn('customer_invoices.customer_id', \Auth::user()->getAccountantCustomersIds())
                        ->whereIn('customer_invoices.status', ['issued', 'paid'])
                        ->whereMonth('customer_invoices.date', date('m'))
                        ->whereYear('customer_invoices.date', date('Y'))
                        ->selectRaw('ROUND(SUM((invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0))* COALESCE(taxes.rate, 0) / 100), 2) as total')
                        ->value('total');

                    $data['totalVatDeductible'] = CustomerExpense::whereIn('customer_id', \Auth::user()->getAccountantCustomersIds())->whereMonth('date', date('m'))
                        ->whereYear('date', date('Y'))
                        ->sum('ttc');

                    $data['totalVatPayable'] = $data['totalVatCollected'] - $data['totalVatDeductible'];
                } else if (\Auth::user()->type == 'company') {

                    $filterIds = \Auth::user()->getCustomerFilterIds();
                    $companyCustomerIds = Customer::whereIn('created_by', $filterIds)->pluck('id');

                    $data['latestIncome']  = CustomerInvoice::whereIn('customer_id', $companyCustomerIds)->orderBy('id', 'desc')->limit(5)->get();
                    $data['latestExpense'] = CustomerExpense::whereIn('customer_id', $companyCustomerIds)->orderBy('id', 'desc')->limit(5)->get();

                    $data['totalCustomers'] = Customer::whereIn('created_by', $filterIds)->where('is_active', 1)->count();
                    $data['totalInvoices'] = CustomerInvoice::whereIn('customer_id', $companyCustomerIds)->count();
                    $data['totalExpenses'] = CustomerExpense::whereIn('customer_id', $companyCustomerIds)->count();

                    $baseQuery = CustomerInvoice::join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
                        ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
                        ->whereIn('customer_invoices.customer_id', $companyCustomerIds)
                        ->whereIn('customer_invoices.status', ['issued', 'paid']);

                    $priceFormula = '(invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0)) * (1 + COALESCE(taxes.rate, 0) / 100)';

                    $data['currentMonthRevenue'] = (clone $baseQuery)
                        ->whereMonth('customer_invoices.date', date('m'))
                        ->whereYear('customer_invoices.date', date('Y'))
                        ->selectRaw("SUM($priceFormula) as total")
                        ->value('total');

                    $data['TodayRevenue'] = (clone $baseQuery)
                        ->whereDate('customer_invoices.date', date('Y-m-d'))
                        ->selectRaw("SUM($priceFormula) as total")
                        ->value('total');

                    $data['currentMonthExpense'] = CustomerExpense::whereIn('customer_id', $companyCustomerIds)
                        ->whereMonth('date', date('m'))
                        ->whereYear('date', date('Y'))
                        ->sum('ttc');

                    $data['TodayExpense'] = CustomerExpense::whereIn('customer_id', $companyCustomerIds)
                        ->whereDate('date', date('Y-m-d'))
                        ->sum('ttc');

                    $data['netResult'] = $data['currentMonthRevenue'] - $data['currentMonthExpense'];


                    $data['totalVatCollected'] = CustomerInvoice::join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
                        ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
                        ->whereIn('customer_invoices.customer_id', $companyCustomerIds)
                        ->whereIn('customer_invoices.status', ['issued', 'paid'])
                        ->whereMonth('customer_invoices.date', date('m'))
                        ->whereYear('customer_invoices.date', date('Y'))
                        ->selectRaw('ROUND(SUM((invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0)) * COALESCE(taxes.rate, 0) / 100), 2) as total')
                        ->value('total');

                    $data['totalVatDeductible'] = CustomerExpense::whereIn('customer_id', $companyCustomerIds)->sum('ttc');

                    $data['totalVatPayable'] = $data['totalVatCollected'] - $data['totalVatDeductible'];

                    // Customers per accountant
                    $accountantIds = User::where('created_by', \Auth::user()->creatorId())->where('type', 'accountant')->pluck('id');
                    $data['customersPerAccountant'] = Customer::select('created_by', DB::raw('count(*) as count'))
                        ->whereIn('created_by', $accountantIds)
                        ->groupBy('created_by')
                        ->with('accountant:id,name')
                        ->get();

                    $data['overdueInvoices'] = DB::table('customer_invoices as ci')
                        ->join('customers as c', 'c.id', '=', 'ci.customer_id')
                        ->join('users as u', 'u.id', '=', 'c.created_by')
                        ->select(
                            'ci.customer_id',
                            'u.name as accountant_name',
                            DB::raw('COUNT(ci.id) as count')
                        )
                        ->whereIn('ci.customer_id', $companyCustomerIds)
                        ->where('ci.status', 'issued')
                        ->whereDate('ci.due_date', '<', now())
                        ->groupBy('ci.customer_id', 'u.name')
                        ->orderByDesc('count')
                        ->limit(10)
                        ->get();
                }

                $filterIds = \Auth::user()->getCustomerFilterIds();
                $customers = Customer::whereIn('created_by', $filterIds)->with('accountant')->get();

                $users = User::find(\Auth::user()->creatorId());
                $plan = Plan::find($users->plan);
                if (!empty($plan)) {
                    if ($plan->storage_limit > 0) {
                        $storage_limit = ($users->storage_limit / $plan->storage_limit) * 100;
                    } else {
                        $storage_limit = 0;
                    }
                } else {
                    return view('dashboard.index', $data, compact('users', 'plan'));
                }

                return view('dashboard.index', $data, compact('users', 'plan', 'storage_limit', 'customers'));
            }
        } else {
            if (!file_exists(storage_path() . "/installed")) {
                header('location:install');
                die;
            } else {
                $settings = Utility::settings();
                if ($settings['display_landing_page'] == 'on' && \Schema::hasTable('landing_page_settings')) {
                    return view('landingpage::layouts.landingpage');
                } else {
                    return redirect('login');
                }
            }
        }
    }

    public function signup(Request $request)
    {
        $ref = $request->query('ref');
        $referralCode = null;
        $referralDiscount = null;

        // ✅ Only for guest users
        if (auth()->check()) {
            return redirect('/dashboard');
        }

        if ($ref) {

            $ip = $request->ip();

            $referral = ReferralCode::where('code', $ref)->first();

            // ❌ Invalid
            if (!$referral) {
                return redirect()->route('signup')
                    ->withInput()
                    ->with('error', 'Invalid referral code.');
            }

            // ❌ Inactive
            if (!$referral->is_active) {
                return redirect()->route('signup')
                    ->withInput()
                    ->with('error', 'This referral code is inactive.');
            }

            // ❌ Expired
            if (now()->lt($referral->starts_at) || now()->gt($referral->ends_at)) {
                return redirect()->route('signup')
                    ->withInput()
                    ->with('error', 'This referral code is expired or not yet valid.');
            }

            // ❌ Limit reached
            if ($referral->used_count >= $referral->max_uses) {
                return redirect()->route('signup')
                    ->withInput()
                    ->with('error', 'This referral code has reached its usage limit.');
            }

            // 🔍 Check existing log for this IP + referral
            $log = DB::table('referral_ip_logs')
                ->where('referral_code', $ref)
                ->where('ip_address', $ip)
                ->first();

            // ❌ If already used → block discount completely
            if ($log && $log->is_used) {
                return redirect()->route('signup')
                    ->withInput()
                    ->with('error', 'You have already used this referral code.');
            }

            // ✅ First time visit → create log + increment clicks
            if (!$log) {

                $referral->increment('clicks');

                DB::table('referral_ip_logs')->insert([
                    'referral_code' => $ref,
                    'ip_address'   => $ip,
                    'is_used'      => false,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            // ✅ Store in session
            if (!session()->has('referral_code')) {
                session([
                    'referral_code'     => $referral->code,
                    'referral_discount' => $referral->discount_percentage
                ]);
            }

            $referralCode     = session('referral_code');
            $referralDiscount = session('referral_discount');
        }

        $mobilePlans = MobileUserPlan::with('prices')
            ->where('is_active', 1)
            ->get();

        return view('dashboard.signup', compact(
            'mobilePlans',
            'referralCode',
            'referralDiscount'
        ));
    }

    public function storeMobileCustomer(Request $request)
    {
        // 1. Validation
        $firstAccountant = User::where('type', 'accountant')->orderBy('id', 'asc')->first();
        $createdBy = $firstAccountant?->id ?? 1;

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('customers')->where(fn($q) => $q->where('created_by', $createdBy)),
            ],
            'phone' => 'required',
            'password' => 'required|min:6',
            'mobile_plan_price_id' => 'required|exists:mobile_user_plan_prices,id',
            'referral_discount_amount' => 'required|numeric|min:0',
            'price_after_discount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 2. Fetch required plan data (Failure here triggers Rollback)
            $freePlan = MobileUserPlan::where('slug', 'free')->firstOrFail();
            $freePrice = MobileUserPlanPrice::where('mobile_user_plan_id', $freePlan->id)
                ->where('price', 0)
                ->first();

            // 3. Referral Logic
            $referral = null;
            if ($request->filled('referral_code')) {
                $referral = ReferralCode::where('code', $request->referral_code)
                    ->where('is_active', 1)
                    ->first();
                $referral->increment('used_count');
            }

            // 4. ID Generation (Warning: Better to use Auto-Increment if possible)
            $latest = Customer::where('created_by', $createdBy)->latest('id')->first();
            $latestCustomerId = ($latest ? $latest->id : 0) + 1;

            // 5. Create Customer
            $customer = Customer::create([
                'name'                => $request->full_name,
                'email'               => $request->email,
                'contact'             => $request->phone,
                'password'            => Hash::make($request->password), // Don't forget to hash!
                'is_b2c'              => 1,
                'created_by'          => $createdBy,
                'customer_id'         => $latestCustomerId,
                'app_access_enabled'  => 1,
                'subscription_status' => 'active',
                'referral_code_id'    => $referral?->id,
                'referral_source'     => $request->referral_code,
                'storage_used_mb'     => 0,
                'is_enable_login'     => 1,
            ]);

            $price = MobileUserPlanPrice::with('plan')->findOrFail($request->mobile_plan_price_id);

            $plan = $price->plan;

            $months = [
                'monthly'   => 1,
                'quarterly' => 3,
                'yearly'    => 12
            ];

            $addMonths = $months[$price->billing_cycle] ?? 1;
            $planEndsAt = now()->addMonths($addMonths);

            MobileUserSubscription::create([
                'customer_id' => $customer->id,
                'mobile_user_plan_id' => $plan->id,
                'mobile_user_plan_price_id' => $request->mobile_plan_price_id,
                'referral_code_id' => $customer->referral_code_id,
                'billing_cycle' => $price->billing_cycle,
                'status' => 'active',
                'original_price' => $price->price,
                'referral_discount_amount' => $request->referral_discount_amount,
                'price_paid' => $request->price_after_discount,
                'currency' => $price->currency,
                'refund_status' => 'none',
                'starts_at' => now(),
                'ends_at' => $planEndsAt,
                'renews_at' => $planEndsAt,
                'trial_ends_at' => now()->addDays(7),
                'payment_provider' => 'test',
            ]);

            $customer->update([
                'mobile_user_plan_id' => $plan->id,
                'subscription_status' => 'active',
            ]);

            $ref = session('referral_code');
            $ip  = $request->ip();

            DB::table('referral_ip_logs')
                ->where('referral_code', $ref)
                ->where('ip_address', $ip)
                ->update([
                    'is_used'    => true,
                    'updated_at' => now()
                ]);

            session()->forget(['referral_code', 'referral_discount']);



            $randomStr = Str::random(10);
            $companyID = User::where('id', $createdBy)->pluck('created_by')->first();

            $accounts = [
                ['code' => '5141', 'bank_name' => 'Banque principale'],
                ['code' => '5161', 'bank_name' => 'Caisse'],
            ];

            foreach ($accounts as $acc) {

                $chartOfAccount = ChartOfAccount::where('created_by', $companyID)
                    ->where('code', $acc['code'])
                    ->latest()
                    ->first();

                if (!$chartOfAccount) {
                    continue;
                }

                BankAccount::create([
                    'chart_account_id' => $chartOfAccount->id,
                    'customer_id'      => $customer->id,
                    'holder_name'      => $customer->name,
                    'bank_name'        => $acc['bank_name'],
                    'account_number'   => $randomStr,
                    'opening_balance'  => 0,
                    'contact_number'   => $customer->contact,
                    'created_by'       => $companyID,
                ]);
            }

            DB::commit();

            return view('dashboard.payment_success')->with('success', 'Customer created successfully! Please proceed to select a plan and make payment.');
        } catch (\Exception $e) {
            DB::rollback();
            // Log the error so you can see it in storage/logs/laravel.log
            \Log::error("Subscription Error: " . $e->getMessage());
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function upgradeSubscripton(Request $request)
    {
        if ($request->isMethod('get')) {
            if (! $request->hasValidSignature()) {
                abort(403);
            }

            $customerId = Crypt::decryptString($request->uid);
            $mobilePlans = MobileUserPlan::with('prices')->where('is_active', 1)->get();

            return view('dashboard.upgrade-plan', compact('customerId', 'mobilePlans'));
        } else {

            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'mobile_plan_price_id' => 'required|exists:mobile_user_plan_prices,id',
            ]);

            DB::beginTransaction();

            try {

                $customer = Customer::findOrFail($request->customer_id);

                // =========================
                // ✅ Get selected price
                // =========================
                $price = MobileUserPlanPrice::with('plan')->findOrFail($request->mobile_plan_price_id);

                $plan = $price->plan;

                // =========================
                // 🔥 Referral logic
                // =========================
                $referral = null;
                $discountAmount = 0;

                // if ($customer->referral_code_id) {

                //     $referral = ReferralCode::find($customer->referral_code_id);

                //     if ($referral && $referral->is_active) {

                //         $percentageDiscount = ($price->price * $referral->discount_percentage) / 100;
                //         $fixedDiscount = $referral->discount_amount;

                //         $discountAmount = max($percentageDiscount, $fixedDiscount);
                //         $discountAmount = min($discountAmount, $price->price);
                //     }
                // }

                $finalPrice = $price->price - $discountAmount;

                // =========================
                // ✅ Deactivate old FREE subscription
                // =========================
                MobileUserSubscription::where('customer_id', $customer->id)
                    ->where('status', 'active')
                    ->update([
                        'status' => 'canceled',
                        'canceled_at' => now()
                    ]);

                $months = [
                    'monthly'   => 1,
                    'quarterly' => 3,
                    'yearly'    => 12
                ];

                $addMonths = $months[$price->billing_cycle] ?? 1;
                $planEndsAt = now()->addMonths($addMonths);


                // =========================
                // ✅ Create NEW subscription
                // =========================
                $subscription = MobileUserSubscription::create([
                    'customer_id' => $customer->id,
                    'mobile_user_plan_id' => $plan->id,
                    'mobile_user_plan_price_id' => $price->id,
                    'referral_code_id' => null,
                    'billing_cycle' => $price->billing_cycle,
                    'status' => 'active',
                    'original_price' => $price->price,
                    'referral_discount_amount' => $discountAmount,
                    'price_paid' => $finalPrice,
                    'currency' => $price->currency,
                    'refund_status' => 'none',
                    'starts_at' => now(),
                    'ends_at' => $planEndsAt,
                    'renews_at' => $planEndsAt,
                    'trial_ends_at' => now()->addDays(7),
                    'payment_provider' => 'test',
                ]);

                $customer->update([
                    'mobile_user_plan_id' => $plan->id,
                    'subscription_status' => 'active',
                ]);
                DB::commit();

                // =========================
                // 🚀 Redirect to payment (for now simulate)
                // =========================
                return view('dashboard.payment_success')->with('success', 'Customer created successfully! Please proceed to select a plan and make payment.');
                // return redirect()->route('payment.page', $subscription->id);
            } catch (\Exception $e) {

                DB::rollback();

                return back()->with('error', $e->getMessage());
            }
        }
    }

    public function getOrderChart($arrParam)
    {
        $arrDuration = [];
        if ($arrParam['duration']) {
            if ($arrParam['duration'] == 'week') {
                $previous_week = strtotime("-2 week +1 day");
                for ($i = 0; $i < 14; $i++) {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week                              = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }

        $arrTask          = [];
        $arrTask['label'] = [];
        $arrTask['data']  = [];
        foreach ($arrDuration as $date => $label) {

            $data               = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = $label;
            $arrTask['data'][]  = $data->total;
        }

        return $arrTask;
    }

    public function getIncomeExpenseChartData(Request $request)
    {
        $year = (int)($request->input('year', date('Y')));
        $customerId = $request->input('customer_id');
        $authUser = Auth::user();
        if (\Auth::user()->type == 'company') {
            $filterIds = $authUser->getCustomerFilterIds();
            $baseCustomerIds = Customer::whereIn('created_by', $filterIds)->pluck('id')->toArray();
        } else {
            $baseCustomerIds = $authUser->getAccountantCustomersIds();
        }
        $customerIds = $baseCustomerIds;
        if (!empty($customerId) && $customerId !== 'all') {
            $customerIds = [intval($customerId)];
        }
        $months = array_map(fn($m) => __(date('F', mktime(0, 0, 0, $m, 1))), range(1, 12));
        $priceFormula = '(invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0)) * (1 + COALESCE(taxes.rate, 0) / 100)';

        $monthlyRevenueData = CustomerInvoice::join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
            ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
            ->whereIn('customer_invoices.customer_id', $customerIds)
            ->whereIn('customer_invoices.status', ['issued', 'paid'])
            ->whereYear('customer_invoices.date', $year)
            ->selectRaw("MONTH(customer_invoices.date) as month, SUM($priceFormula) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyExpenseData = CustomerExpense::whereIn('customer_id', $customerIds)
            ->whereYear('date', $year)
            ->selectRaw('month(date) as month, sum(ttc) as total')
            ->groupBy('month')
            ->pluck('total', 'month');
        $incomeArr = [];
        $expenseArr = [];
        for ($i = 1; $i <= 12; $i++) {
            $incomeArr[] = number_format($monthlyRevenueData->get($i, 0), 2, '.', '');
            $expenseArr[] = number_format($monthlyExpenseData->get($i, 0), 2, '.', '');
        }
        return response()->json([
            'month' => $months,
            'data' => [
                'income' => $incomeArr,
                'expense' => $expenseArr,
            ],
            'year' => $year,
        ]);
    }


    public function getSummaryMetrics(Request $request)
    {
        $auth = Auth::user();
        if (\Auth::user()->type == 'company') {
            $filterIds = $auth->getCustomerFilterIds();
            $baseCustomerIds = Customer::whereIn('created_by', $filterIds)->pluck('id')->toArray();
        } else {
            $baseCustomerIds = $auth->getAccountantCustomersIds();
        }
        $customerId = $request->input('customer_id');
        $customerIds = $baseCustomerIds;
        if (!empty($customerId) && $customerId !== 'all') {
            $customerIds = [intval($customerId)];
        }
        $baseQuery = CustomerInvoice::join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
            ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
            ->whereIn('customer_invoices.customer_id', $customerIds)
            ->whereIn('customer_invoices.status', ['issued', 'paid']);

        $priceFormula = '(invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0)) * (1 + COALESCE(taxes.rate, 0) / 100)';

        $todayRevenue = (clone $baseQuery)
            ->whereDate('customer_invoices.date', date('Y-m-d'))
            ->selectRaw("SUM($priceFormula) as total")
            ->value('total');

        $currentMonthRevenue = (clone $baseQuery)
            ->whereMonth('customer_invoices.date', date('m'))
            ->whereYear('customer_invoices.date', date('Y'))
            ->selectRaw("SUM($priceFormula) as total")
            ->value('total');
        $todayExpense = CustomerExpense::whereIn('customer_id', $customerIds)
            ->whereDate('date', date('Y-m-d'))
            ->sum('ttc');

        $currentMonthExpense = CustomerExpense::whereIn('customer_id', $customerIds)
            ->whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))
            ->sum('ttc');
        $netResult = $currentMonthRevenue - $currentMonthExpense;
        $vatFormula = '(invoice_articles.total_price_ht - COALESCE(invoice_articles.discount, 0)) * COALESCE(taxes.rate, 0) / 100';

        $totalVatCollected = CustomerInvoice::join('invoice_articles', 'customer_invoices.id', '=', 'invoice_articles.invoice_id')
            ->leftJoin('taxes', 'invoice_articles.tva_percentage', '=', 'taxes.id')
            ->whereIn('customer_invoices.customer_id', $customerIds)
            ->whereIn('customer_invoices.status', ['issued', 'paid'])
            ->whereMonth('customer_invoices.date', date('m'))
            ->whereYear('customer_invoices.date', date('Y'))
            ->selectRaw("ROUND(SUM($vatFormula), 2) as total")
            ->value('total');
        $totalVatDeductible = CustomerExpense::whereIn('customer_id', $customerIds)
            ->whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))
            ->sum('ttc');
        $totalVatPayable = $totalVatCollected - $totalVatDeductible;
        return response()->json([
            'TodayRevenue' => $auth->priceFormat($todayRevenue),
            'TodayExpense' => $auth->priceFormat($todayExpense),
            'currentMonthRevenue' => $auth->priceFormat($currentMonthRevenue),
            'currentMonthExpense' => $auth->priceFormat($currentMonthExpense),
            'netResult' => $auth->priceFormat($netResult),
            'totalVatPayable' => $auth->priceFormat($totalVatPayable),
        ]);
    }

    /**
     * Send notification/email to one or many clients from accountant dashboard.
     */
    public function sendClientsNotification(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'recipients' => 'required',
            'document' => 'nullable|file|max:10240',
        ]);

        $auth = Auth::user();

        // Determine accessible customer owners: company and accountant (if accountant)
        $creatorId = $auth->creatorId();
        $ownerIds = [$creatorId];
        if ($auth->type === 'accountant') {
            $ownerIds[] = $auth->id;
        }

        // recipients can be 'all' or array of customer ids
        $customers = collect();
        if ($request->recipients === 'all') {
            $filterIds = \Auth::user()->getCustomerFilterIds();
            $customers = Customer::whereIn('created_by', $filterIds)->get();
        } else {
            $ids = is_array($request->recipients) ? $request->recipients : explode(',', $request->recipients);
            $customers = Customer::whereIn('id', $ids)->get();
        }

        if ($customers->isEmpty()) {
            return back()->with('error', __('No recipients found.'));
        }

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentPath = $file->store('client_notifications', 'public');
        }

        Utility::getSMTPDetails(1);
        $attachmentPath = $documentPath ? Storage::disk('public')->path($documentPath) : null;

        // Create client notifications
        foreach ($customers as $cust) {
            ClientNotification::create([
                'customer_id' => $cust->id,
                'sender_id' => $auth->id,
                'title' => $request->subject,
                'message' => $request->message,
                'is_read' => false,
                'data' => $request->notification_type ?? null,
                'document' => $documentPath,
            ]);

            if (!empty($cust->email)) {
                try {

                    $html = view('email.client_notification', [
                        'subject' => $request->subject,
                        'messageContent' => $request->message
                    ])->render();

                    Mail::send([], [], function ($message) use ($cust, $request, $attachmentPath, $html) {

                        $message->to($cust->email)
                            ->subject($request->subject)
                            ->html($html);

                        if ($attachmentPath) {
                            $message->attach($attachmentPath);
                        }
                    });
                } catch (\Exception $e) {
                }
            }
        }

        return back()->with('success', __('Sent successfully.'));
    }

    public function getClientsNotifications()
    {
        $auth = Auth::user();
        $customerIds = $auth->type === 'company' ? Customer::whereIn('created_by', $auth->getCustomerFilterIds())->pluck('id') : $auth->getAccountantCustomersIds();
        $notifications = ClientNotification::whereIn('customer_id', $customerIds)->orderBy('created_at', 'desc')->with('customer', 'sender')->get();
        return view('dashboard.clients_notifications', compact('notifications'));
    }


    public function destroy($id)
    {
        $notification = ClientNotification::where('id', $id)->firstOrFail();

        // Check if document exists and delete it from storage
        if (!empty($notification->document)) {
            // Change 'local' to 'public'
            if (Storage::disk('public')->exists($notification->document)) {
                Storage::disk('public')->delete($notification->document);
            }
        }

        $notification->delete();

        return back()->with('success', __('Notification deleted.'));
    }

    // Clear all notifications and all associated files
    public function clearAll()
    {
        $notifications = ClientNotification::where('customer_id', Auth::guard('customer')->user()->id)->get();

        foreach ($notifications as $note) {
            if (!empty($note->document)) {
                // Change 'local' to 'public'
                if (Storage::disk('public')->exists($note->document)) {
                    Storage::disk('public')->delete($note->document);
                }
            }
            $note->delete();
        }

        return back()->with('success', __('All notifications and documents cleared.'));
    }
}
