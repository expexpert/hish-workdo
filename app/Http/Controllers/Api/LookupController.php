<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\ProductServiceCategory;

class LookupController extends Controller
{
    public function getTransactionResources()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'accounts' => BankAccount::select('id', 'holder_name as name')->get(),
                'categories' => ProductServiceCategory::select('id', 'name')->where('type', '=', 'income')->get(),
            ]
        ]);
    }
}