<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Authenticatable
{
    use HasRoles;
    use Notifiable;
    use Impersonate;
    use HasApiTokens;

    protected $guard_name = 'web';
    protected $appends = ['avatar_url', 'signature_url'];
    protected $casts = [
        'password_changed_at' => 'datetime',
        'is_b2c' => 'boolean',
        'app_access_enabled' => 'boolean',
        'storage_used_mb' => 'integer',
    ];

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'bio',
        'short_bio',
        'ice_number',
        'rc_number',
        'patent_number',
        'if_number',
        'cnss',
        'rib',
        'company_type',
        'company_color',
        'phone',
        'address',
        'vat_number',
        'website',
        'notes',
        'customer_type',
        'is_b2c',
        'mobile_user_plan_id',
        'storage_used_mb',
        'app_access_enabled',
        'subscription_status',
        'password',
        'password_changed_at',
        'contact',
        'avatar',
        'signature',
        'is_active',
        'is_enable_login',
        'created_by',
        'email_verified_at',
        'billing_name',
        'billing_country',
        'billing_state',
        'billing_city',
        'billing_phone',
        'billing_zip',
        'billing_address',
        'shipping_name',
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_phone',
        'shipping_zip',
        'shipping_address',
        'last_login_at',
        'bot_otp',
        'bot_pending_phone',
        'bot_otp_expires_at',
        'bot_active',
        'bot_verified_at',
        'bot_lang',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public $settings;

    public function authId()
    {
        return $this->id;
    }

    public function creatorId()
    {
        if ($this->type == 'company' || $this->type == 'super admin') {
            return $this->id;
        } else {
            return $this->created_by;
        }
    }

    public function creatorId1()
    {
        if (\Auth::guard('customer')->check()) {
            return $this->id;
        } else {
            return $this->created_by;
        }
    }

    public function companyId()
    {
        return $this->accountant?->creatorId() ?? $this->created_by;
    }

    public function mobilePlan()
    {
        return $this->belongsTo(MobileUserPlan::class, 'mobile_user_plan_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(MobileUserSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(MobileUserSubscription::class)
            ->where('status', 'active')
            ->latest();
    }


    // Helpers

    public function isB2C()
    {
        return $this->is_b2c;
    }

    public function hasActiveSubscription()
    {
        return $this->activeSubscription()->exists();
    }

    public function hasStorageAvailable($sizeMb)
    {
        if (!$this->mobilePlan) {
            return true;
        }

        return ($this->storage_used_mb + $sizeMb) <= $this->mobilePlan->storage_limit_mb;
    }

    public function currentLanguage()
    {
        return $this->lang;
    }

    public function priceFormat($price)
    {
        $settings = Utility::settings();

        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, Utility::getValByName('decimal_number')) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public function currencySymbol()
    {
        $settings = Utility::settings();

        return $settings['site_currency_symbol'];
    }

    public function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public function timeFormat($time)
    {
        $settings = Utility::settings();

        return date($settings['site_time_format'], strtotime($time));
    }

    public static function invoiceNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }


    public function quoteNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["quote_prefix"] . sprintf("%05d", $number);
    }

    public static function retainerNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["retainer_prefix"] . sprintf("%05d", $number);
    }

    public static function proposalNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["proposal_prefix"] . sprintf("%05d", $number);
    }

    public function invoiceChartData()
    {
        $month[]       = __('January');
        $month[]       = __('February');
        $month[]       = __('March');
        $month[]       = __('April');
        $month[]       = __('May');
        $month[]       = __('June');
        $month[]       = __('July');
        $month[]       = __('August');
        $month[]       = __('September');
        $month[]       = __('October');
        $month[]       = __('November');
        $month[]       = __('December');
        $data['month'] = $month;

        $data['currentYear'] = date('M-Y');

        $totalInvoice = Invoice::where('customer_id', \Auth::user()->id)->count();
        $unpaidArr    = array();




        for ($i = 1; $i <= 12; $i++) {
            $unpaidInvoice  = Invoice::where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->whereRaw('month(`send_date`) = ?', $i)->where('status', '1')->where('due_date', '>', date('Y-m-d'))->get();
            $paidInvoice    = Invoice::where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->whereRaw('month(`send_date`) = ?', $i)->where('status', '4')->get();
            $partialInvoice = Invoice::where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->whereRaw('month(`send_date`) = ?', $i)->where('status', '3')->get();
            $dueInvoice     = Invoice::where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->whereRaw('month(`send_date`) = ?', $i)->where('status', '1')->where('due_date', '<', date('Y-m-d'))->get();


            $totalUnpaid = 0;
            for ($j = 0; $j < count($unpaidInvoice); $j++) {
                $unpaidAmount = $unpaidInvoice[$j]->getDue();
                $totalUnpaid  += $unpaidAmount;
            }

            $totalPaid = 0;
            for ($j = 0; $j < count($paidInvoice); $j++) {
                $paidAmount = $paidInvoice[$j]->getTotal();
                $totalPaid  += $paidAmount;
            }

            $totalPartial = 0;
            for ($j = 0; $j < count($partialInvoice); $j++) {
                $partialAmount = $partialInvoice[$j]->getDue();
                $totalPartial  += $partialAmount;
            }

            $totalDue = 0;
            for ($j = 0; $j < count($dueInvoice); $j++) {
                $dueAmount = $dueInvoice[$j]->getDue();
                $totalDue  += $dueAmount;
            }

            $unpaidData[]  = $totalUnpaid;
            $paidData[]    = $totalPaid;
            $partialData[] = $totalPartial;
            $dueData[]     = $totalDue;

            $statusData['unpaid']  = $unpaidData;
            $statusData['paid']    = $paidData;
            $statusData['partial'] = $partialData;
            $statusData['due']     = $dueData;
        }

        $data['data'] = $statusData;


        $unpaidInvoice  = Invoice::where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->where('status', '1')->where('due_date', '>', date('Y-m-d'))->get();
        $paidInvoice    = Invoice::where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->where('status', '4')->get();
        $partialInvoice = Invoice::where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->where('status', '3')->get();
        $dueInvoice     = Invoice::where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->where('status', '1')->where('due_date', '<', date('Y-m-d'))->get();

        $progressData['totalInvoice']        = $totalInvoice = Invoice::where('customer_id', \Auth::user()->id)->whereRaw('year(`send_date`) = ?', array(date('Y')))->count();
        $progressData['totalUnpaidInvoice']  = $totalUnpaidInvoice = count($unpaidInvoice);
        $progressData['totalPaidInvoice']    = $totalPaidInvoice = count($paidInvoice);
        $progressData['totalPartialInvoice'] = $totalPartialInvoice = count($partialInvoice);
        $progressData['totalDueInvoice']     = $totalDueInvoice = count($dueInvoice);

        $progressData['unpaidPr']  = ($totalInvoice != 0) ? ($totalUnpaidInvoice * 100) / $totalInvoice : 0;
        $progressData['paidPr']    = ($totalInvoice != 0) ? ($totalPaidInvoice * 100) / $totalInvoice : 0;
        $progressData['partialPr'] = ($totalInvoice != 0) ? ($totalPartialInvoice * 100) / $totalInvoice : 0;
        $progressData['duePr']     = ($totalInvoice != 0) ? ($totalDueInvoice * 100) / $totalInvoice : 0;

        $progressData['unpaidColor']  = '#fc544b';
        $progressData['paidColor']    = '#63ed7a';
        $progressData['partialColor'] = '#6777ef';
        $progressData['dueColor']     = '#ffa426';

        $data['progressData'] = $progressData;


        return $data;
    }


    public function customerInvoice($customerId)
    {
        $invoices  = Invoice::where('customer_id', $customerId)->orderBy('issue_date', 'desc')->get();
        $proposals = Proposal::where('customer_id', $customerId)->orderBy('issue_date', 'desc')->get()->toArray();

        return $invoices;
    }

    public function customerProposal($customerId)
    {
        $proposals = Proposal::where('customer_id', $customerId)->orderBy('issue_date', 'desc')->get();

        return $proposals;
    }

    public function customerOverdue($customerId)
    {
        $dueInvoices = Invoice::where('customer_id', $customerId)->whereNotIn(
            'status',
            [
                '0',
                '4',
            ]
        )->where('due_date', '<', date('Y-m-d'))->get();
        $due         = 0;
        foreach ($dueInvoices as $invoice) {
            $due += $invoice->getDue();
        }

        return $due;
    }

    public function customerPaid($customerId)
    {
        $paidInvoices = Invoice::where('customer_id', $customerId)->whereNotIn(
            'status',
            [
                '0',
                '1',
            ]
        )->get();

        $paid         = 0;
        foreach ($paidInvoices as $invoice) {
            $paid += ($invoice->getTotal() - $invoice->getDue());
        }

        return $paid;
    }

    public function customerTotalInvoiceSum($customerId)
    {
        $invoices = Invoice::where('customer_id', $customerId)->get();
        $total    = 0;
        foreach ($invoices as $invoice) {
            $total += $invoice->getTotal();
        }

        return $total;
    }

    public function customerTotalInvoice($customerId)
    {
        $invoices = Invoice::where('customer_id', $customerId)->count();

        return $invoices;
    }
    public function contractNumberFormat($number)
    {
        $settings = Utility::settings();
        return $settings["contract_prefix"] . sprintf("%05d", $number);
    }

    public function monthStatuses(): HasMany
    {
        return $this->hasMany(CustomerMonthStatus::class, 'customer_id');
    }

    public function clientBankStatements(): HasMany
    {
        return $this->hasMany(ClientBankStatement::class, 'customer_id');
    }

    public function accountant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return null;
        }

        return asset('storage/' . $this->avatar);
    }

    public function getSignatureUrlAttribute()
    {
        if (!$this->signature) {
            return null;
        }

        return asset('storage/' . $this->signature);
    }

    protected static function booted()
    {
        static::updating(function ($customer) {
            // Check if the password hash is being modified
            if ($customer->isDirty('password')) {
                $customer->password_changed_at = now();
            }
        });
    }
}
