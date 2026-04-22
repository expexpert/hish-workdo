<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\CustomerCategory;
use App\Models\ProductServiceCategory;
use App\Models\CustomerClient;
use App\Models\CustomerSupplier;
use App\Models\Tax;
use App\Models\Invoice;
use App\Models\ProductServiceUnit;

class LookupController extends Controller
{
    public function getTransactionResources()
    {
        $company_id = auth()->user()->companyId();

        $suppliersQuery = CustomerSupplier::where('customer_id', auth()->id())->select('id', 'supplier_name as name');

        if (request()->query('sort') === 'recent') {
            $suppliersQuery->withMax('expenses', 'created_at')
                ->orderByRaw('expenses_max_created_at IS NULL, expenses_max_created_at DESC');
        } else {
            $suppliersQuery->orderBy('supplier_name', 'asc');
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

        return response()->json([
            'status' => 'success',
            'data' => [
                'clients' => $clientsQuery->get(),
                'accounts' => BankAccount::where('created_by', $company_id)->select('id', 'holder_name as name')->get(),
                'invoice_number' => $invoice_number
            ]
        ]);
    }

    function invoiceNumber()
    {
        $company_id = auth()->user()->companyId();

        $latest = Invoice::where('created_by', '=', $company_id)->latest()->first();
        if (!$latest) {
            return 1;
        }

        return $latest->invoice_id + 1;
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
