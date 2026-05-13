<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\CustomerCategory;
use App\Models\ProductServiceCategory;
use App\Models\CustomerClient;
use App\Models\CustomerInvoice;
use App\Models\Vender;
use App\Models\CustomerQuote;
use App\Models\Tax;
use App\Models\Invoice;
use App\Models\ProductServiceUnit;

class LookupController extends Controller
{
    public function getTransactionResources()
    {
        $company_id = auth()->user()->companyId();

        $suppliersQuery = Vender::where('customer_id', auth()->id())->where('created_by', $company_id)->select('id', 'name', 'company_name');

        if (request()->query('sort') === 'recent') {
            $suppliersQuery->withMax('expenses', 'created_at')
                ->orderByRaw('expenses_max_created_at IS NULL, expenses_max_created_at DESC');
        } else {
            $suppliersQuery->orderBy('name', 'asc');
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'accounts' => BankAccount::where('created_by', $company_id)->select('id', 'holder_name as name')->get(),
                // 'categories' => ProductServiceCategory::select('id', 'name')->where('type', '=', 'income')->get(),
                'categories' => CustomerCategory::where('is_active', '=', 1)->select('id', 'name')->orderBy('name', 'asc')->get(),
                'suppliers' => $suppliersQuery->get(),
            ]
        ]);
    }


    public function getCustomerClientResources()
    {
        $company_id = auth()->user()->companyId();

        $clientsQuery = CustomerClient::where('customer_id', auth()->id())->select('id', 'client_name');

        if (request()->query('sort') === 'recent') {
            $clientsQuery->withMax('invoices', 'created_at')
                ->orderByRaw('invoices_max_created_at IS NULL, invoices_max_created_at DESC');
        } else {
            $clientsQuery->orderBy('client_name', 'asc');
        }

        $invoice_number = \Auth::user()->invoiceNumberFormat($this->invoiceNumber());
        $quote_number = \Auth::user()->quoteNumberFormat($this->quoteNumber());

        return response()->json([
            'status' => 'success',
            'data' => [
                'clients' => $clientsQuery->get(),
                'accounts' => BankAccount::where('created_by', $company_id)->select('id', 'holder_name as name')->get(),
                'invoice_number' => $invoice_number,
                'quote_number' => $quote_number,
            ]
        ]);
    }

    function invoiceNumber()
    {
        $latest = CustomerInvoice::where('customer_id', auth()->id())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->invoice_number + 1;
    }

    function quoteNumber()
    {
        $latest = CustomerQuote::where('customer_id', auth()->id())->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->quote_number + 1;
    }


    public function getProductResources()
    {
        $company_id = auth()->user()->companyId();

        return response()->json([
            'status' => 'success',
            'data' => [
                'tax' => Tax::where('created_by', $company_id)->get(),
                'units' => ProductServiceUnit::where('created_by', $company_id)->get(),
                'categories' => ProductServiceCategory::where('created_by', $company_id)->where('type', 'product & service')->get(),
            ]
        ]);
    }
}
