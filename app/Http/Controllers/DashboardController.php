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
                    $data['latestIncome']  = Revenue::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                    $data['latestExpense'] = Payment::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();

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

                return view('dashboard.index', $data, compact('users', 'plan', 'storage_limit'));
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
            $customers = Customer::whereIn('created_by', $ownerIds)->where('is_active', 1)->get();
        } else {
            $ids = is_array($request->recipients) ? $request->recipients : explode(',', $request->recipients);
            $customers = Customer::whereIn('id', $ids)->whereIn('created_by', $ownerIds)->get();
        }

        if ($customers->isEmpty()) {
            return back()->with('error', __('No recipients found.'));
        }

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            // $documentPath = $file->store('client_notifications', 'public');
            $documentPath = $file->store('client_notifications', 'local');
        }

        // Create client notifications
        foreach ($customers as $cust) {
            ClientNotification::create([
                'customer_id' => $cust->id,
                'sender_id' => $auth->id,
                'title' => $request->subject,
                'message' => $request->message,
                'is_read' => false,
                'data' => null,
                'document' => $documentPath,
            ]);
        }

        return back()->with('success', __('Notification sent successfully.'));
    }


    public function destroy($id)
    {
        $notification = ClientNotification::where('id', $id)
            ->where('customer_id', Auth::guard('customer')->user()->id)
            ->firstOrFail();

        // Check if document exists and delete it from storage
        if (!empty($notification->document)) {
            if (Storage::disk('local')->exists($notification->document)) {
                Storage::disk('local')->delete($notification->document);
            }
        }

        $notification->delete();

        return back()->with('success', __('Notification and associated document deleted.'));
    }

    // Clear all notifications and all associated files
    public function clearAll()
    {
        $notifications = ClientNotification::where('customer_id', Auth::guard('customer')->user()->id)->get();

        foreach ($notifications as $note) {
            if (!empty($note->document)) {
                if (Storage::disk('local')->exists($note->document)) {
                    Storage::disk('local')->delete($note->document);
                }
            }
            $note->delete();
        }

        return back()->with('success', __('All notifications and documents cleared.'));
    }
}
