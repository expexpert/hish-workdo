<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Observers\AdminActivityObserver;
use App\Models\User;
use App\Models\Customer;
use App\Models\Vender;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\InvoicePayment;
use App\Models\Bill;
use App\Models\BillProduct;
use App\Models\BillPayment;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\Proposal;
use App\Models\ProposalProduct;
use App\Models\Retainer;
use App\Models\RetainerProduct;
use App\Models\RetainerPayment;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\ProductServiceUnit;
use App\Models\Expense;
use App\Models\ChartOfAccount;
use App\Models\BankAccount;
use App\Models\BankTransfer;
use App\Models\Coupon;
use App\Models\Contract;
use App\Models\ContractNote;
use App\Models\Budget;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
         Schema::defaultStringLength(191);

        // Register AdminActivityObserver for all models
        // Employee/User Management
        User::observe(AdminActivityObserver::class);
        
        // Customer & Vendor Management
        Customer::observe(AdminActivityObserver::class);
        Vender::observe(AdminActivityObserver::class);
        
        // Invoice Management
        Invoice::observe(AdminActivityObserver::class);
        InvoiceProduct::observe(AdminActivityObserver::class);
        InvoicePayment::observe(AdminActivityObserver::class);
        
        // Bill Management
        Bill::observe(AdminActivityObserver::class);
        BillProduct::observe(AdminActivityObserver::class);
        BillPayment::observe(AdminActivityObserver::class);
        
        // Credit & Debit Notes
        CreditNote::observe(AdminActivityObserver::class);
        DebitNote::observe(AdminActivityObserver::class);
        
        // Proposal Management
        Proposal::observe(AdminActivityObserver::class);
        ProposalProduct::observe(AdminActivityObserver::class);
        
        // Retainer Management
        Retainer::observe(AdminActivityObserver::class);
        RetainerProduct::observe(AdminActivityObserver::class);
        RetainerPayment::observe(AdminActivityObserver::class);
        
        // Product & Service Management
        ProductService::observe(AdminActivityObserver::class);
        ProductServiceCategory::observe(AdminActivityObserver::class);
        ProductServiceUnit::observe(AdminActivityObserver::class);
        
        // Expense & Financial Management
        Expense::observe(AdminActivityObserver::class);
        ChartOfAccount::observe(AdminActivityObserver::class);
        BankAccount::observe(AdminActivityObserver::class);
        BankTransfer::observe(AdminActivityObserver::class);
        
        // Journal & Accounting
        JournalEntry::observe(AdminActivityObserver::class);
        JournalItem::observe(AdminActivityObserver::class);
        
        // Other Management
        Coupon::observe(AdminActivityObserver::class);
        Contract::observe(AdminActivityObserver::class);
        ContractNote::observe(AdminActivityObserver::class);
        Budget::observe(AdminActivityObserver::class);

        RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });
    }
}


