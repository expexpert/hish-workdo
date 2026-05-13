<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomField;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Utility;
use App\Models\ProductServiceCategory;
use Auth;
use File;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerExport;
use App\Imports\CustomerImport;
use App\Models\ClientTransaction;
use App\Models\ClientBankStatement;
use App\Models\CustomerExpense;
use App\Models\CustomerInvoice;
use App\Models\InvoiceArticle;
use App\Models\InvoiceProduct;
use App\Models\CustomerQuote;
use App\Models\ChartOfAccount;
use App\Models\BankAccount;
use App\Models\MobileUserSubscription;
use App\Models\Bill;
use App\Models\BillProduct;
use App\Models\ProductService;
use App\Models\Tax;
use App\Models\ProductServiceUnit;
use App\Models\Vender;
use App\Models\MobileUserPlan;
use App\Models\MobileUserPlanPrice;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerInvitationMail;

class CustomerController extends Controller
{

    public function dashboard()
    {
        $data['invoiceChartData'] = \Auth::user()->invoiceChartData();
        $customer = \Auth::user();
        $data['notifications'] = \App\Models\ClientNotification::where('customer_id', $customer->id)->orderBy('created_at', 'desc')->limit(20)->get();

        return view('customer.dashboard', $data);
    }

    public function index()
    {
        if (\Auth::user()->can('manage customer')) {
            $filterIds = \Auth::user()->getCustomerFilterIds();
            $customers = Customer::whereIn('created_by', $filterIds)->with('accountant')->get();
            $isAccountant = \Auth::user()->type == 'accountant';

            $B2CCustomers = Customer::where('is_b2c', 1)->get();

            return view('customer.index', compact('customers', 'isAccountant', 'B2CCustomers'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create customer')) {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'customer')->get();
            $accountant = User::where('created_by', \Auth::user()->creatorId())->where('type', 'accountant')->pluck('name', 'id');

            return view('customer.create', compact('customFields', 'accountant'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {

        if (\Auth::user()->can('create customer')) {

            $rules = [
                'name' => 'required',
                'contact' => 'required|regex:/^\+\d{1,3}\d{9,13}$/',
                'email' => [
                    'required',
                    Rule::unique('customers')->where(function ($query) {
                        return $query->where('created_by', \Auth::user()->id);
                    })
                ],

            ];


            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->route('customer.index')->with('error', $messages->first());
            }

            $enableLogin       = 0;
            if (!empty($request->password_switch) && $request->password_switch == 'on') {
                $enableLogin   = 1;
                $validator = \Validator::make(
                    $request->all(),
                    ['password' => 'required|min:6']
                );

                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }
            }
            $userpassword               = $request->input('password');

            $objCustomer    = \Auth::user();
            $creator        = User::find($objCustomer->creatorId());
            $total_customer = $objCustomer->countCustomers();
            $plan           = Plan::find($creator->plan);

            $default_language = DB::table('settings')->select('value')->where('name', 'default_language')->where('created_by', \Auth::user()->id)->first();
            if ($total_customer < $plan->max_customers || $plan->max_customers == -1) {
                $customer                  = new Customer();
                $customer->customer_id     = $this->customerNumber();
                $customer->name            = $request->name;
                $customer->contact         = $request->contact;
                $customer->email           = $request->email;
                $customer->tax_number      = $request->tax_number;

                $request['password'] = !empty($userpassword) ? \Hash::make($userpassword) : null;

                $customer->created_by      = $request->accountant;
                $customer->billing_name    = $request->billing_name;
                $customer->billing_country = $request->billing_country;
                $customer->billing_state   = $request->billing_state;
                $customer->billing_city    = $request->billing_city;
                $customer->billing_phone   = $request->billing_phone;
                $customer->billing_zip     = $request->billing_zip;
                $customer->billing_address = $request->billing_address;
                if (!empty($request['password'])) {
                    $customer->password        = $request['password'] ?? null;
                }
                $customer->shipping_name    = $request->shipping_name;
                $customer->shipping_country = $request->shipping_country;
                $customer->shipping_state   = $request->shipping_state;
                $customer->shipping_city    = $request->shipping_city;
                $customer->shipping_phone   = $request->shipping_phone;
                $customer->shipping_zip     = $request->shipping_zip;
                $customer->shipping_address = $request->shipping_address;

                $customer->is_enable_login =  $enableLogin;

                $customer->lang = !empty($default_language) ? $default_language->value : 'en';

                $customer->save();
                CustomField::saveData($customer, $request->customField);



                $freePlan = MobileUserPlan::where('name', 'Free')->first();
                $price    = MobileUserPlanPrice::with('plan')->findOrFail($freePlan->id);

                $plan = $price->plan;

                MobileUserSubscription::create([
                    'customer_id' => $customer->id,
                    'mobile_user_plan_id' => $plan->id,
                    'mobile_user_plan_price_id' => $price->id,
                    'referral_code_id' => null,
                    'billing_cycle' => $price->billing_cycle,
                    'status' => 'active',
                    'original_price' => $price->price,
                    'referral_discount_amount' => 0,
                    'price_paid' => $price->price,
                    'currency' => $price->currency,
                    'refund_status' => 'none',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonths(1),
                    'renews_at' => now()->addMonths(1),
                    'trial_ends_at' => now()->addDays(7),
                    'payment_provider' => 'test',
                ]);

                $customer->update([
                    'mobile_user_plan_id' => $plan->id,
                    'subscription_status' => 'active',
                    'is_enable_login'  => 1,
                ]);


                $randomStr = Str::random(10);
                $creatorId = \Auth::user()->creatorId();

                $accounts = [
                    ['code' => '5141', 'bank_name' => 'Banque principale'],
                    ['code' => '5161', 'bank_name' => 'Caisse'],
                ];

                foreach ($accounts as $acc) {

                    $chartOfAccount = ChartOfAccount::where('created_by', $creatorId)
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
                        'created_by'       => $creatorId,
                    ]);
                }
            } else {
                return redirect()->back()->with('error', __('Your user limit is over, Please upgrade plan.'));
            }


            $role_r = Role::where('name', '=', 'customer')->firstOrFail();
            $customer->assignRole($role_r);

            $uArr = [
                'email' => $customer->email,
                'password' => $userpassword,
            ];

            try {
                $resp = Utility::sendEmailTemplate('user_created', [$customer->id => $customer->email], $uArr);
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }


            //Twilio Notification
            $setting  = Utility::settings(\Auth::user()->creatorId());
            if (isset($setting['customer_notification']) && $setting['customer_notification'] == 1) {
                $uArr = [
                    'customer_name' => $request->name,
                    'email'  => $request->email,
                    'password'  =>  $userpassword,
                ];
                Utility::send_twilio_msg($request->contact, 'new_customer', $uArr);
            }

            // webhook
            $module = 'New Customer';
            $webhook =  Utility::webhookSetting($module);

            if ($webhook) {
                $parameter = json_encode($customer);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);


                if ($status == true) {
                    return redirect()->route('customer.index')->with('success', __('Customer successfully created.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
                } else {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }


            return redirect()->route('customer.index')->with('success', __('Customer successfully created.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show($ids)
    {

        $id       = \Crypt::decrypt($ids);
        $customer = Customer::find($id);
        return view('customer.show', compact('customer'));
    }

    public function markNotificationRead($id)
    {
        $notification = \App\Models\ClientNotification::where('id', $id)->where('customer_id', \Auth::user()->id)->first();
        if ($notification) {
            $notification->is_read = true;
            $notification->save();
        }

        return redirect()->back();
    }


    public function edit($id)
    {
        if (\Auth::user()->can('edit customer')) {
            $customer              = Customer::find($id);
            $customer->customField = CustomField::getData($customer, 'customer');

            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'customer')->get();

            $accountant = User::where('created_by', \Auth::user()->creatorId())->where('type', 'accountant')->pluck('name', 'id');

            return view('customer.edit', compact('customer', 'customFields', 'accountant'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, Customer $customer)
    {
        if (!\Auth::user()->can('edit customer')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        // ✅ Validation
        $rules = [
            'name'      => 'required|string|max:255',
            'contact'   => 'required|regex:/^\+\d{1,3}\d{9,13}$/',
            'email'     => 'required|email|unique:customers,email,' . $customer->id,
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('customer.index')
                ->with('error', $validator->getMessageBag()->first());
        }

        // ✅ Assign fields
        $customer->fill([
            'name'             => $request->name,
            'contact'          => $request->contact,
            'email'            => $request->email,
            'tax_number'       => $request->tax_number,
            'created_by'       => $request->accountant,
            'billing_name'     => $request->billing_name,
            'billing_country'  => $request->billing_country,
            'billing_state'    => $request->billing_state,
            'billing_city'     => $request->billing_city,
            'billing_phone'    => $request->billing_phone,
            'billing_zip'      => $request->billing_zip,
            'billing_address'  => $request->billing_address,
            'shipping_name'    => $request->shipping_name,
            'shipping_country' => $request->shipping_country,
            'shipping_state'   => $request->shipping_state,
            'shipping_city'    => $request->shipping_city,
            'shipping_phone'   => $request->shipping_phone,
            'shipping_zip'     => $request->shipping_zip,
            'shipping_address' => $request->shipping_address,
            'company_type'     => $request->company_type,
            'bio'              => $request->bio,
            'address'          => $request->address,
            'website'          => $request->website,
            'vat_number'       => $request->vat_number,
            'ice_number'       => $request->ice_number,
            'rc_number'        => $request->rc_number,
            'patent_number'    => $request->patent_number,
            'if_number'        => $request->if_number,
            'cnss'             => $request->cnss,
            'rib'              => $request->rib,
        ]);

        // ✅ Avatar Upload
        if ($request->hasFile('avatar')) {
            if (!empty($customer->avatar) && Storage::disk('public')->exists($customer->avatar)) {
                Storage::disk('public')->delete($customer->avatar);
            }

            $customer->avatar = $request->file('avatar')
                ->store('avatars', 'public');
        }

        // ✅ Signature Upload
        if ($request->hasFile('signature')) {
            if (!empty($customer->signature) && Storage::disk('public')->exists($customer->signature)) {
                Storage::disk('public')->delete($customer->signature);
            }

            $customer->signature = $request->file('signature')
                ->store('signatures', 'public');
        }

        $customer->save();

        // ✅ Custom fields safe call
        if ($request->has('customField')) {
            CustomField::saveData($customer, $request->customField);
        }

        return redirect()->route('customer.index')
            ->with('success', __('Customer successfully updated.'));
    }


    public function destroy(Customer $customer)
    {
        $authorizedIds = \Auth::user()->getCustomerFilterIds();
        if (\Auth::user()->can('delete customer')) {
            if (in_array($customer->created_by, $authorizedIds) || $customer->created_by == \Auth::id()) {
                $customer->delete();

                return redirect()->route('customer.index')->with('success', __('Customer successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function customerNumber()
    {
        $latest = Customer::where('created_by', '=', \Auth::user()->customerFilterId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->customer_id + 1;
    }

    public function customerLogout(Request $request)
    {
        \Auth::guard('customer')->logout();

        $request->session()->invalidate();

        return redirect()->route('customer.login');
    }

    public function payment(Request $request)
    {

        if (\Auth::user()->can('manage customer payment')) {

            $category = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->where('type', 2)->get()->pluck('name', 'id');
            $query = Transaction::where('user_id', \Auth::user()->id)->where('user_type', 'Customer')->where('type', 'Payment');
            if (isset($request->date) && !empty($request->date)) {
                $time = strtotime($request->date);
                $month = date("m", $time);

                $query = $query->whereMonth('date', $month);
            }

            if (!empty($request->category)) {
                $query->where('category', '=', $request->category);
            }
            $payments = $query->get();

            return view('customer.payment', compact('payments', 'category'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function transaction(Request $request)
    {
        if (\Auth::user()->can('manage customer payment')) {
            $category = [
                'Invoice' => 'Invoice',
                'Retainer' => 'Retainer',
            ];

            $query = Transaction::where('user_id', \Auth::user()->id)->where('user_type', 'Customer');

            if (isset($request->date) && !empty($request->date)) {
                $time = strtotime($request->date);
                $month = date("m", $time);

                $query = $query->whereMonth('date', $month);
            }

            if (!empty($request->category)) {
                $query->where('category', '=', $request->category);
            }
            $transactions = $query->get();

            return view('customer.transaction', compact('transactions', 'category'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function profile()
    {
        $userDetail              = \Auth::user();
        $userDetail->customField = CustomField::getData($userDetail, 'customer');
        $customFields            = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'customer')->get();

        return view('customer.profile', compact('userDetail', 'customFields'));
    }

    public function editprofile(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);

        $this->validate(
            $request,
            [
                'name' => 'required|max:120',
                'email' => 'required|email|unique:users,email,' . $userDetail['id'],
            ]
        );

        if ($request->hasFile('profile')) {
            if (\Auth::guard('customer')->check()) {
                $file_path = $user['avatar'];
                $filenameWithExt = $request->file('profile')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('profile')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $settings = Utility::getStorageSetting();

                if ($settings['storage_setting'] == 'local') {
                    $dir        = 'uploads/avatar/';
                } else {
                    $dir        = 'uploads/avatar';
                }
                $image_path = $dir . $userDetail['avatar'];

                $url = '';
                $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);
                if ($path['flag'] == 1) {
                    $url = $path['url'];
                } else {
                    return redirect()->route('profile', \Auth::user()->id)->with('error', __($path['msg']));
                }
            } else {
                $file_path = $user['avatar'];
                $image_size = $request->file('profile')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if ($result == 1) {

                    Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                    $filenameWithExt = $request->file('profile')->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $request->file('profile')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                    $settings = Utility::getStorageSetting();

                    if ($settings['storage_setting'] == 'local') {
                        $dir        = 'uploads/avatar/';
                    } else {
                        $dir        = 'uploads/avatar';
                    }
                    $image_path = $dir . $userDetail['avatar'];

                    $url = '';
                    $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);
                    if ($path['flag'] == 1) {
                        $url = $path['url'];
                    } else {
                        return redirect()->route('profile', \Auth::user()->id)->with('error', __($path['msg']));
                    }
                } else {
                    return redirect()->back()->with('error', $result);
                }
            }
        }

        if (!empty($request->profile)) {
            $user['avatar'] = $fileNameToStore;
        }
        $user['name']    = $request['name'];
        $user['email']   = $request['email'];
        $user['contact'] = $request['contact'];
        $user->save();
        CustomField::saveData($user, $request->customField);

        return redirect()->back()->with(
            'success',
            __('Profile successfully updated.') . ((isset($result) && $result != 1) ? '<br> <span class="text-danger">' . $result . '</span>' : '')
        );
    }

    public function editBilling(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);
        $this->validate(
            $request,
            [
                'billing_name' => 'required',
                'billing_country' => 'required',
                'billing_state' => 'required',
                'billing_city' => 'required',
                'billing_phone' => 'required',
                'billing_zip' => 'required',
                'billing_address' => 'required',
            ]
        );
        $input = $request->all();
        $user->fill($input)->save();

        return redirect()->back()->with(
            'success',
            'Profile successfully updated.'
        );
    }

    public function editShipping(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);
        $this->validate(
            $request,
            [
                'shipping_name' => 'required',
                'shipping_country' => 'required',
                'shipping_state' => 'required',
                'shipping_city' => 'required',
                'shipping_phone' => 'required',
                'shipping_zip' => 'required',
                'shipping_address' => 'required',
            ]
        );
        $input = $request->all();
        $user->fill($input)->save();

        return redirect()->back()->with(
            'success',
            'Profile successfully updated.'
        );
    }

    public function updatePassword(Request $request)
    {
        if (Auth::Check()) {
            $request->validate(
                [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
                ]
            );
            $objUser          = Auth::user();
            $request_data     = $request->All();
            $current_password = $objUser->password;
            if (Hash::check($request_data['current_password'], $current_password)) {
                $user_id            = Auth::User()->id;
                $obj_user           = Customer::find($user_id);
                $obj_user->password = Hash::make($request_data['new_password']);;
                $obj_user->save();

                return redirect()->back()->with('success', __('Password updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('Please enter correct current password.'));
            }
        } else {
            return redirect()->back()->with('error', __('Something is wrong.'));
        }
    }

    public function changeLanquage($lang)
    {
        $user       = Auth::user();
        $user->lang = $lang;
        $user->save();
        if ($user->lang == 'ar' || $user->lang == 'he') {
            $value = 'on';
        } else {
            $value = 'off';
        }
        if ($user->type == 'super admin') {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                [
                    $value,
                    'SITE_RTL',
                    $user->creatorId(),
                    date('Y-m-d H:i:s'),
                    date('Y-m-d H:i:s'),
                ]
            );
        } else {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                [
                    $value,
                    'SITE_RTL',
                    $user->creatorId(),
                    date('Y-m-d H:i:s'),
                    date('Y-m-d H:i:s'),
                ]
            );
        }

        return redirect()->back()->with('success', __('Language change successfully.'));
    }

    public function export()
    {
        $name = 'customer_' . date('Y-m-d i:h:s');
        $data = Excel::download(new CustomerExport(), $name . '.xlsx');

        return $data;
    }

    public function importFile()
    {
        return view('customer.import');
    }

    public function import(Request $request)
    {

        $rules = [
            'file' => 'required|mimes:csv,txt,xls',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $customers = (new CustomerImport())->toArray(request()->file('file'))[0];

        $totalCustomer = count($customers) - 1;
        $errorArray    = [];
        $customer_id = $this->customerNumber();

        for ($i = 1; $i <= count($customers) - 1; $i++) {
            $cust_id = $customer_id++;
            $customer = $customers[$i];
            $customerByEmail = Customer::where('email', $customer[1])->first();

            if (!empty($customerByEmail)) {
                $customerData = $customerByEmail;
            } else {
                $customerData = new Customer();
                $customerData->customer_id      = $cust_id;
            }


            $customerData->name             = $customer[0] ?? "";
            $customerData->email            = $customer[1] ?? "";
            $customerData->password         = Hash::make($customer[2]);
            $customerData->contact          = $customer[3] ?? "";
            $customerData->billing_name     = $customer[4] ?? "";
            $customerData->billing_country  = $customer[5] ?? "";
            $customerData->billing_state    = $customer[6] ?? "";
            $customerData->billing_city     = $customer[7] ?? "";
            $customerData->billing_phone    = $customer[8] ?? "";
            $customerData->billing_zip      = $customer[9] ?? "";
            $customerData->billing_address  = $customer[10] ?? "";
            $customerData->shipping_name    = $customer[11] ?? "";
            $customerData->shipping_country = $customer[12] ?? "";
            $customerData->shipping_state   = $customer[13] ?? "";
            $customerData->shipping_city    = $customer[14] ?? "";
            $customerData->shipping_phone   = $customer[15] ?? "";
            $customerData->shipping_zip     = $customer[16] ?? "";
            $customerData->shipping_address = $customer[17] ?? "";
            $customerData->lang             = 'en';
            $customerData->is_active        = 1;
            $customerData->created_by       = \Auth::user()->creatorId();

            if (empty($customerData)) {
                $errorArray[] = $customerData;
            } else {
                $customerData->save();

                $role_r = Role::where('name', '=', 'customer')->firstOrFail();
                $customerData->assignRole($role_r);
            }
        }

        $errorRecord = [];
        if (empty($errorArray)) {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        } else {
            $data['status'] = 'error';
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalCustomer . ' ' . 'record');


            foreach ($errorArray as $errorData) {

                $errorRecord[] = implode(',', $errorData);
            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

    public function previewInvoice()
    {
        $objUser  = \Auth::user();
        $settings = Utility::settings();

        $invoice  = new Invoice();

        $customer                   = new \stdClass();
        $customer->email            = '<Email>';
        $customer->shipping_name    = '<Customer Name>';
        $customer->shipping_country = '<Country>';
        $customer->shipping_state   = '<State>';
        $customer->shipping_city    = '<City>';
        $customer->shipping_phone   = '<Customer Phone Number>';
        $customer->shipping_zip     = '<Zip>';
        $customer->shipping_address = '<Address>';
        $customer->billing_name     = '<Customer Name>';
        $customer->billing_country  = '<Country>';
        $customer->billing_state    = '<State>';
        $customer->billing_city     = '<City>';
        $customer->billing_phone    = '<Customer Phone Number>';
        $customer->billing_zip      = '<Zip>';
        $customer->billing_address  = '<Address>';
        $invoice->sku               = 'Test123';

        $totalTaxPrice = 0;
        $taxesData     = [];

        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $item           = new \stdClass();
            $item->name     = 'Item ' . $i;
            $item->quantity = 1;
            $item->tax      = 5;
            $item->discount = 50;
            $item->price    = 100;

            $taxes = [
                'Tax 1',
                'Tax 2',
            ];

            $itemTaxes = [];
            foreach ($taxes as $k => $tax) {
                $taxPrice         = 10;
                $totalTaxPrice    += $taxPrice;
                $itemTax['name']  = 'Tax ' . $k;
                $itemTax['rate']  = '10 %';
                $itemTax['price'] = '$10';
                $itemTaxes[]      = $itemTax;
                if (array_key_exists('Tax ' . $k, $taxesData)) {
                    $taxesData['Tax ' . $k] = $taxesData['Tax 1'] + $taxPrice;
                } else {
                    $taxesData['Tax ' . $k] = $taxPrice;
                }
            }
            $item->itemTax = $itemTaxes;
            $items[]       = $item;
        }

        $invoice->invoice_id = 1;
        $invoice->issue_date = date('Y-m-d H:i:s');
        $invoice->due_date   = date('Y-m-d H:i:s');
        $invoice->itemData   = $items;

        $invoice->totalTaxPrice = 60;
        $invoice->totalQuantity = 3;
        $invoice->totalRate     = 300;
        $invoice->totalDiscount = 10;
        $invoice->taxesData     = $taxesData;
        $invoice->customField   = [];
        $customFields           = [];

        $preview    = 1;


        $logo         = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $img          = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));

        return view('customer.show', compact('invoice', 'preview', 'img', 'settings', 'customer', 'customFields'));
    }

    public function statement(Request $request, $id)
    {
        $customer = Customer::find($id);
        $settings = Utility::settings();
        $customerDetail = Customer::findOrFail($customer['id']);
        $invoice = Invoice::where('created_by', '=', \Auth::user()->creatorId())->where('customer_id', '=', $customer->id)->get()->pluck('id');
        $invoice_payment = InvoicePayment::whereIn('invoice_id', $invoice);

        if (!empty($request->from_date) && !empty($request->until_date)) {
            $invoice_payment->whereBetween('date', [$request->from_date, $request->until_date]);
            $data['from_date'] = $request->from_date;
            $data['until_date'] = $request->until_date;
        } else {
            $data['from_date'] = date('Y-m-01');
            $data['until_date'] = date('Y-m-t');
            $invoice_payment->whereBetween('date', [$data['from_date'], $data['until_date']]);
        }

        $invoice_payment = $invoice_payment->get();

        // Get unique invoice IDs from payments
        $payment_invoice_ids = $invoice_payment->pluck('invoice_id')->unique();

        // Get only invoices that have payments in the date range
        $invoice_total = Invoice::whereIn('id', $payment_invoice_ids)->get();

        $user = \Auth::user();
        $logo = asset(Storage::url('uploads/logo/'));
        $company_logo = Utility::getValByName('company_logo_dark');
        $img = asset($logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png'));

        $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'invoice')->get();

        return view('customer.statement', compact('customer', 'img', 'user', 'customerDetail', 'invoice_payment', 'settings', 'data', 'invoice_total'));
    }

    public function customerPassword($id)
    {
        $eId        = \Crypt::decrypt($id);
        $customer = Customer::find($eId);

        return view('customer.reset', compact('customer'));
    }

    public function customerPasswordReset(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'password' => 'required|confirmed|same:password_confirmation',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $customer                 = Customer::where('id', $id)->first();
        $customer->forceFill([
            'password' => Hash::make($request->password),
            'is_enable_login' => 1,
        ])->save();

        return redirect()->route('customer.index')->with(
            'success',
            'Customer Password successfully updated.'
        );
    }


    public function getClientTransactions(Request $request)
    {
        $user = Auth::user();
        $query = ClientTransaction::with(['account:id,holder_name', 'category:id,name'])
            ->orderBy('transaction_date', 'desc');

        if ($user->type == 'accountant') {
            // Transactions for customers created by this specific accountant
            $query->whereIn('customer_id', function ($q) use ($user) {
                $q->select('id')->from('customers')->where('created_by', $user->id);
            });
        } else if ($user->type == 'company') {
            // Transactions for customers created by ANY accountant belonging to this company
            $query->whereIn('customer_id', function ($q) use ($user) {
                $q->select('id')->from('customers')->whereIn('created_by', function ($subQ) use ($user) {
                    // Fetch IDs of accountants created by this company
                    $subQ->select('id')->from('users')->where('created_by', $user->id);
                });
            });
        }

        $transactions = $query->get();

        return view('ClientReport.transaction', compact('transactions'));
    }

    public function getClientBankStatements(Request $request)
    {
        $user = Auth::user();
        $query = ClientBankStatement::orderBy('month_year', 'desc');

        if ($user->type == 'accountant') {
            // Filter statements for customers created by this specific accountant
            $query->whereIn('customer_id', function ($q) use ($user) {
                $q->select('id')
                    ->from('customers')
                    ->where('created_by', $user->id);
            });
        } else if ($user->type == 'company') {
            // Filter statements for customers created by ANY accountant belonging to this company
            $query->whereIn('customer_id', function ($q) use ($user) {
                $q->select('id')
                    ->from('customers')
                    ->whereIn('created_by', function ($subQ) use ($user) {
                        // Get all accountant IDs where the company is the creator
                        $subQ->select('id')
                            ->from('users')
                            ->where('created_by', $user->id);
                    });
            });
        }

        // Filtering
        if (!empty($request->start_date) || !empty($request->end_date)) {
            $startDate = !empty($request->start_date) ? \Carbon\Carbon::parse($request->start_date)->startOfMonth() : null;
            $endDate = !empty($request->end_date) ? \Carbon\Carbon::parse($request->end_date)->endOfMonth() : null;

            if ($startDate && $endDate) {
                $months = [];
                $currentDate = clone $startDate;
                while ($currentDate->lte($endDate)) {
                    $months[] = $currentDate->format('m-Y');
                    $currentDate->addMonth();
                }
                $query->whereIn('month_year', $months);
            } elseif ($startDate) {
                // If only start date, filter for all months after start date
                // Since month_year is a string, we might need a more complex query or filter in collection
                // But typically for reports we want a range. If we have to filter strings like '01-2024', 
                // it's tricky in SQL. Let's just handle it as a range if both are present for now, 
                // or just convert start_date to its month.
                $query->where('month_year', $startDate->format('m-Y'));
            } elseif ($endDate) {
                $query->where('month_year', $endDate->format('m-Y'));
            }
        }

        if (!empty($request->customer)) {
            $query->where('customer_id', $request->customer);
        }

        $bankStatements = $query->get();

        // Get customers for filter dropdown
        $customers = Customer::query();
        if ($user->type == 'accountant') {
            $customers->where('created_by', $user->id);
        } else if ($user->type == 'company') {
            $customers->whereIn('created_by', function ($subQ) use ($user) {
                $subQ->select('id')->from('users')->where('created_by', $user->id);
            });
        }
        $customer = $customers->pluck('name', 'id')->toArray();
        $customer = ['' => __('Select Customer')] + $customer;

        return view('ClientReport.statement', compact('bankStatements', 'customer'));
    }


    public function showFile(ClientBankStatement $bankStatement)
    {
        $user = \Auth::user();
        if (
            !in_array($user->type, ['company', 'accountant']) ||
            \App\Services\AdminActivityLogger::isImpersonating() ||
            !$bankStatement->customer ||
            !$bankStatement->customer->accountant ||
            $user->creatorId() != $bankStatement->customer->accountant->creatorId()
        ) {
            abort(403, 'Unauthorized access.');
        }

        // --- Handle Platform-Wide Storage ---
        $storage_settings = Utility::getStorageSetting();
        $disk = $storage_settings['storage_setting'] ?? 'public';
        $upload_disk = ($disk == 'local' || $disk == '') ? 'public' : $disk;

        if (Storage::disk($upload_disk)->exists('bank_statements/' . $bankStatement->file_path)) {
            return Storage::disk($upload_disk)->response('bank_statements/' . $bankStatement->file_path);
        }

        // Fallback for private disk or transition
        if (Storage::disk('private')->exists($bankStatement->file_path)) {
            return Storage::disk('private')->response($bankStatement->file_path);
        }

        if (Storage::disk('public')->exists('bank_statements/' . $bankStatement->file_path)) {
            return Storage::disk('public')->response('bank_statements/' . $bankStatement->file_path);
        }

        abort(404);
    }

    public function showExpenseFile(CustomerExpense $expense)
    {
        $user = \Auth::user();
        if (
            !in_array($user->type, ['company', 'accountant']) ||
            \App\Services\AdminActivityLogger::isImpersonating() ||
            !$expense->customer ||
            !$expense->customer->accountant ||
            $user->creatorId() != $expense->customer->accountant->creatorId()
        ) {
            abort(403, 'Unauthorized access.');
        }

        // --- Handle Platform-Wide Storage ---
        $storage_settings = Utility::getStorageSetting();
        $disk = $storage_settings['storage_setting'];
        $upload_disk = ($disk == 'local' || $disk == '') ? 'private' : $disk;

        // Try the exact path in the DB
        if ($expense->file && Storage::disk($upload_disk)->exists($expense->file)) {
            return Storage::disk($upload_disk)->response($expense->file);
        }

        // Fallback for old public/expenses/ path logic
        if ($expense->file && Storage::disk('public')->exists('expenses/' . $expense->file)) {
            return Storage::disk('public')->response('expenses/' . $expense->file);
        }

        // Fallback for when 'expenses/' wasn't in the DB path but is on the disk
        if ($expense->file && !str_starts_with($expense->file, 'expenses/') && Storage::disk($upload_disk)->exists('expenses/' . $expense->file)) {
            return Storage::disk($upload_disk)->response('expenses/' . $expense->file);
        }

        abort(404);
    }

    public function showInvoiceFile(CustomerInvoice $invoice)
    {
        $user = \Auth::user();
        if (
            !in_array($user->type, ['company', 'accountant']) ||
            \App\Services\AdminActivityLogger::isImpersonating() ||
            !$invoice->customer ||
            !$invoice->customer->accountant ||
            $user->creatorId() != $invoice->customer->accountant->creatorId()
        ) {
            abort(403, 'Unauthorized access.');
        }

        $storage_settings = Utility::getStorageSetting();
        $disk = $storage_settings['storage_setting'];
        $upload_disk = ($disk == 'local' || $disk == '') ? 'private' : $disk;

        if (!$invoice->document_path || !Storage::disk($upload_disk)->exists($invoice->document_path)) {
            abort(404);
        }

        return Storage::disk($upload_disk)->response($invoice->document_path);
    }

    public function showQuoteFile(CustomerQuote $quote)
    {
        $user = \Auth::user();

        if (
            !in_array($user->type, ['company', 'accountant']) ||
            \App\Services\AdminActivityLogger::isImpersonating() ||
            !$quote->customer ||
            !$quote->customer->accountant ||
            $user->creatorId() != $quote->customer->accountant->creatorId()
        ) {
            abort(403, 'Unauthorized access.');
        }

        $storage_settings = Utility::getStorageSetting();
        $disk = $storage_settings['storage_setting'];
        $upload_disk = ($disk == 'local' || $disk == '') ? 'private' : $disk;

        if (!$quote->document_path || !Storage::disk($upload_disk)->exists($quote->document_path)) {
            abort(404);
        }

        return Storage::disk($upload_disk)->response($quote->document_path);
    }


    public function getExpenses(Request $request)
    {
        $user = Auth::user();
        $query = CustomerExpense::with(['category:id,name', 'supplier:id,name'])
            ->orderBy('date', 'desc');

        if ($user->type == 'accountant') {
            // Expenses for customers created by this specific accountant
            $query->whereIn('customer_id', function ($q) use ($user) {
                $q->select('id')->from('customers')->where('created_by', $user->id);
            });
        } else if ($user->type == 'company') {
            // Expenses for customers created by ANY accountant belonging to this company
            $query->whereIn('customer_id', function ($q) use ($user) {
                $q->select('id')->from('customers')->whereIn('created_by', function ($subQ) use ($user) {
                    // Fetch IDs of accountants created by this company
                    $subQ->select('id')->from('users')->where('created_by', $user->id);
                });
            });
        }

        // Filtering
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif (!empty($request->start_date)) {
            $query->where('date', '>=', $request->start_date);
        } elseif (!empty($request->end_date)) {
            $query->where('date', '<=', $request->end_date);
        }

        if (!empty($request->customer)) {
            $query->where('customer_id', $request->customer);
        }

        $expenses = $query->get();

        // Get customers for filter dropdown
        $customers = Customer::query();
        if ($user->type == 'accountant') {
            $customers->where('created_by', $user->id);
        } else if ($user->type == 'company') {
            $customers->whereIn('created_by', function ($subQ) use ($user) {
                $subQ->select('id')->from('users')->where('created_by', $user->id);
            });
        }
        $customer = $customers->pluck('name', 'id')->toArray();
        $customer = ['' => __('Select Customer')] + $customer;

        return view('ClientReport.expense', compact('expenses', 'customer'));
    }


    public function getInvoices(Request $request)
    {
        $user = Auth::user();
        $data = [];
        $status = $request->input('status', '');
        $query = CustomerInvoice::with([
            'customer:id,name,created_by',
            'customer.accountant:id,name',
            'client:id,client_name',
            'articles'
        ])
            ->orderBy('date', 'desc');

        if ($user->type == 'accountant') {
            // Invoices for customers created by this specific accountant
            $query->whereIn('customer_id', function ($q) use ($user) {
                $q->select('id')->from('customers')->where('created_by', $user->id);
            });
        } else if ($user->type == 'company') {
            // Invoices for customers created by ANY accountant belonging to this company
            $query->whereIn('customer_id', function ($q) use ($user) {
                $q->select('id')->from('customers')->whereIn('created_by', function ($subQ) use ($user) {
                    // Fetch IDs of accountants created by this company
                    $subQ->select('id')->from('users')->where('created_by', $user->id);
                });
            });
        }

        // Filtering
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif (!empty($request->start_date)) {
            $query->where('date', '>=', $request->start_date);
        } elseif (!empty($request->end_date)) {
            $query->where('date', '<=', $request->end_date);
        }

        if (!empty($request->customer)) {
            $query->where('customer_id', $request->customer);
        }

        $invoices = $query->latest()->get();

        $data['totalInvoiceCount'] = $invoices->count();
        $data['totalPendingInvoiceCount'] = $invoices->where('review_status', 'PENDING')->count();
        $data['totalApprovedInvoiceCount'] = $invoices->where('review_status', 'VALIDATED')->count();
        $data['totalRejectedInvoiceCount'] = $invoices->where('review_status', 'REJECTED')->count();

        $query->when($status, function ($q) use ($status) {
            $q->where('review_status', $status);
        });
        $invoices = $query->get();

        // Get customers for filter dropdown
        $customers = \App\Models\Customer::query();
        if ($user->type == 'accountant') {
            $customers->where('created_by', $user->id);
        } else if ($user->type == 'company') {
            $customers->whereIn('created_by', function ($subQ) use ($user) {
                $subQ->select('id')->from('users')->where('created_by', $user->id);
            });
        }
        $customer = $customers->pluck('name', 'id')->toArray();
        $customer = ['' => __('Select Customer')] + $customer;

        return view('ClientReport.invoice', compact('invoices', 'data', 'customer'));
    }


    public function getQuotes(Request $request)
    {
        $user = Auth::user();
        $data = [];
        $status = $request->input('status', '');
        $query = CustomerQuote::with([
            'customer:id,name,created_by',
            'customer.accountant:id,name',
            'client:id,client_name',
            'articles:id,quotes_id,designation,unit_price_ht,quantity,total_price_ht,tva_percentage',
            'articles.tax'
        ])
            ->orderBy('date', 'desc');

        if ($user->type == 'accountant') {
            // Quotes for customers created by this specific accountant
            $query->whereIn('customer_id', function ($q) use ($user) {
                $q->select('id')->from('customers')->where('created_by', $user->id);
            });
        } else if ($user->type == 'company') {
            // Quotes for customers created by ANY accountant belonging to this company
            $query->whereIn('customer_id', function ($q) use ($user) {
                $q->select('id')->from('customers')->whereIn('created_by', function ($subQ) use ($user) {
                    // Fetch IDs of accountants created by this company
                    $subQ->select('id')->from('users')->where('created_by', $user->id);
                });
            });
        }

        // Filtering
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif (!empty($request->start_date)) {
            $query->where('date', '>=', $request->start_date);
        } elseif (!empty($request->end_date)) {
            $query->where('date', '<=', $request->end_date);
        }

        if (!empty($request->customer)) {
            $query->where('customer_id', $request->customer);
        }

        $quotes = $query->get();

        $query->when($status, function ($q) use ($status) {
            $q->where('review_status', $status);
        });
        $quotes = $query->get();

        // Get customers for filter dropdown
        $customers = \App\Models\Customer::query();
        if ($user->type == 'accountant') {
            $customers->where('created_by', $user->id);
        } else if ($user->type == 'company') {
            $customers->whereIn('created_by', function ($subQ) use ($user) {
                $subQ->select('id')->from('users')->where('created_by', $user->id);
            });
        }
        $customer = $customers->pluck('name', 'id')->toArray();
        $customer = ['' => __('Select Customer')] + $customer;

        return view('ClientReport.quote', compact('quotes', 'data', 'customer'));
    }


    public function invoiceNumber()
    {
        $latest = Invoice::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->invoice_id + 1;
    }

    public function invoiceReviewAction(Request $request)
    {
        $invoice = CustomerInvoice::findOrFail($request->invoice_id);

        $invoice->review_status = $request->action;
        $invoice->save();

        $categoryID = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
            ->where('type', 'Income')
            ->first()
            ?->id ?? 1;

        $InvoiceArticle = InvoiceArticle::where('invoice_id', $invoice->id)->get();
        $alreadyValidated = Invoice::where('ref_number', \Auth::user()->invoiceNumberFormatNew($invoice->invoice_number))->where('customer_id', $invoice->customer_id)->exists();


        if ($request->action == 'VALIDATED' && !$alreadyValidated) {
            $newInvoice = new Invoice();
            $newInvoice->invoice_id     = $this->invoiceNumber();
            $newInvoice->customer_id    = $invoice->customer_id;
            $newInvoice->status         = 0;
            $newInvoice->issue_date     = $invoice->date;
            $newInvoice->due_date       = $invoice->due_date;
            $newInvoice->send_date      = $invoice->created_at->format('Y-m-d');
            $newInvoice->category_id    = $categoryID;
            $newInvoice->ref_number     = \Auth::user()->invoiceNumberFormatNew($invoice->invoice_number);
            $newInvoice->discount_apply = isset($invoice->discount_apply) ? 1 : 0;
            $newInvoice->created_by     = \Auth::user()->creatorId();

            $newInvoice->save();
            Utility::starting_number($newInvoice->invoice_id + 1, 'invoice');


            foreach ($InvoiceArticle as $article) {
                $newArticle = new InvoiceProduct();
                $newArticle->invoice_id  = $newInvoice->id;
                $newArticle->product_id  = $article->product_id;
                $newArticle->quantity    = $article->quantity;
                $newArticle->tax         = $article->tva_percentage;
                $newArticle->discount    = $article->discount;
                $newArticle->price       = $article->unit_price_ht;
                $newArticle->description = null;
                $newArticle->save();

                Utility::total_quantity('minus', $newArticle->quantity, $newArticle->product_id);

                //Product Stock Report
                $type = 'invoice';
                $type_id = $newInvoice->id;
                $description = $newArticle->quantity . '  ' . __(' quantity sold in invoice') . ' ' . \Auth::user()->invoiceNumberFormatNew($newInvoice->invoice_id, $newInvoice->created_at);
                Utility::addProductStock($newArticle->product_id, $newArticle->quantity, $type, $description, $type_id);
            }
        }


        $counts = [
            'total'     => CustomerInvoice::count(),
            'pending'   => CustomerInvoice::where('review_status', 'PENDING')->orWhere('review_status', '')->count(),
            'validated' => CustomerInvoice::where('review_status', 'VALIDATED')->count(),
            'rejected'  => CustomerInvoice::where('review_status', 'REJECTED')->count(),
        ];

        return response()->json([
            'success' => true,
            'counts'  => $counts,
            'message' => 'Review status updated'
        ]);
    }


    public function expenseReviewAction(Request $request)
    {

        $expense = CustomerExpense::findOrFail($request->expense_id);

        $categoryID = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())
            ->where('type', 'expense')
            ->first()
            ?->id ?? 1;

        $product = ProductService::where('created_by', \Auth::user()->creatorId())->where('name', $expense->category->name)->first();

        if (!$product) {

            $user = \Auth::user()->creatorId();
            $tax = Tax::where('created_by', $user)->where('rate', 0)->first();
            $unit = ProductServiceUnit::where('created_by', $user)->where('name', 'service')->first();
            $accounts = ChartOfAccount::where('created_by', $user)->pluck('id', 'code');

            $sale_chartaccount_id = $accounts['71243'] ?? 1;
            $expense_chartaccount_id = $accounts['61711'] ?? 1;

            $product = new ProductService();
            $product->name = $expense->category->name;
            $product->created_by = \Auth::user()->creatorId();
            $product->sku =  rand(100000, 999999);
            $product->type =  'Product';
            $product->sale_price =  0;
            $product->purchase_price =  0;
            $product->quantity =  1;
            $product->tax_id =  $tax->id;
            $product->unit_id =  $unit->id;
            $product->category_id =  $categoryID;
            $product->sale_chartaccount_id =  $sale_chartaccount_id;
            $product->expense_chartaccount_id =  $expense_chartaccount_id;
            $product->save();
        }

        $alreadyValidated = (bool) $expense->bill_status;

        if ($request->action == 'VALIDATED' && !$alreadyValidated) {
            $bill                 = new Bill();
            $bill->bill_id        = $this->billNumber();
            $bill->vender_id      = $expense->supplier_id;
            $bill->bill_date      = $expense->date;
            $bill->status         = 0;
            $bill->due_date       = $expense->date;
            $bill->category_id    = $categoryID;
            $bill->order_number   = 0;
            $bill->discount_apply = 0;
            $bill->created_by     = \Auth::user()->creatorId();
            $bill->customer_id     = $expense->customer_id;

            $bill->save();
            Utility::starting_number($bill->bill_id + 1, 'bill');


            $billProduct              = new BillProduct();
            $billProduct->bill_id     = $bill->id;
            $billProduct->product_id  = $product->id;
            $billProduct->quantity    = 1;
            $billProduct->tax         = $product->tax_id;
            $billProduct->discount    = 0;
            $billProduct->price       = $expense->total_ttc;
            $billProduct->save();

            $billTotal = 0;
            $total_amount = 0;


            Utility::total_quantity('plus', $billProduct->quantity, $billProduct->product_id);

            if (!empty($product->id)) {
                $type = 'bill';
                $type_id = $bill->id;
                $description = $product->quantity . '  ' . __('quantity purchase in bill') . ' ' . \Auth::user()->billNumberFormat($bill->bill_id);
                Utility::addProductStock($product->id, $product->quantity, $type, $description, $type_id);
                $total_amount += ($billProduct->quantity * $billProduct->price) + $billTotal;
            }


            $setting  = Utility::settings(\Auth::user()->creatorId());
            $billId    = Crypt::encrypt($bill->id);
            $bill->url = route('bill.pdf', $billId);
            $vendor = Vender::find($request->vender_id);
            if (isset($setting['bill_notification']) && $setting['bill_notification'] == 1) {
                $uArr = [
                    'bill_name' => $vendor->name,
                    'bill_number'  => \Auth::user()->billNumberFormat($bill->bill_id),
                    'bill_url'  =>  $bill->url,
                ];
                Utility::send_twilio_msg($vendor->contact, 'new_bill', $uArr);
            }

            // webhook
            $module = 'New Bill';
            $webhook =  Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($bill);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    return redirect()->route('bill.index', $bill->id)->with('success', __('Bill successfully created.'));
                } else {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }

            $expense->review_status = $request->action;
            $expense->bill_status = true;
            $expense->save();
        } else {
            $expense->review_status = $request->action;
            $expense->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Review status updated'
        ]);
    }

    function billNumber()
    {
        $latest = Bill::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->bill_id + 1;
    }


    public function mobileCustomers()
    {
        $customers = Customer::where('is_b2c', 1)->with('accountant')->get();
        $isAccountant = '0';

        return view('customer.index', compact('customers', 'isAccountant'));
    }

    public function mobileCustomerDestroy($id)
    {
        $customer = Customer::where('is_b2c', 1)->findOrFail($id);
        MobileUserSubscription::where('customer_id', $customer->id)->delete();
        $customer->delete();

        return redirect()->route('mobile.customers')->with('success', __('Customer successfully deleted.'));
    }

    public function mobileCustomerUpdate(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->is_enable_login = $request->is_enable_login;
        $customer->save();

        return redirect()->back()->with('success', 'Customer access updated successfully.');
    }



    public function inviteCustomer(Request $request)
    {
        $accountant = auth()->user();

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return back()->with('error', 'No customer found with that email address.');
        }

        if ($customer->is_b2c == 0) {
            return back()->with('error', 'This customer is already associated with an accountant.');
        }

        $encryptedCustomer = Crypt::encryptString($customer->id);

        $encryptedAccountant = Crypt::encryptString($accountant->id);

        $url = URL::temporarySignedRoute(
            'customer.invite.accept',
            now()->addHours(48),
            [
                'customer' => $encryptedCustomer,
                'accountant' => $encryptedAccountant,
            ]
        );

        Utility::getSMTPDetailsNew($accountant->creatorId());

        try {
            Mail::to($customer->email)->send(new CustomerInvitationMail($url, $accountant, $customer));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send invitation email: ' . $e->getMessage());
        }

        return back()->with('success', 'Invitation sent successfully.');
    }

    public function acceptInvitation(Request $request)
    {
        try {
            $customerId = Crypt::decryptString($request->customer);
            $accountantId = Crypt::decryptString($request->accountant);

            $customer = Customer::findOrFail($customerId);
            $accountant = User::findOrFail($accountantId);

            // Assign the accountant to the customer
            $customer->created_by = $accountant->id;
            $customer->is_b2c = 0;
            $customer->save();


            $randomStr = Str::random(10);
            $companyID = $accountant->created_by;

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


            return view('dashboard.customer_merge')->with('success', 'Invitation accepted successfully. You are now connected with ' . $accountant->name);
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Invalid or expired invitation link.');
        }
    }
}
