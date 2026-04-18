<?php

namespace App\Traits;

use App\Models\CustomerCategory;
use App\Models\CustomerSupplier;
use App\Models\CustomerClient;
use App\Models\Tax;
use Illuminate\Http\Request;
use Carbon\Carbon;

trait HandlesBotInputs
{
    /**
     * Map bot-specific name inputs to database IDs
     */
    protected function mapBotInputs(Request $request)
    {
        $user = \Auth::user();
        if (!$user) return;

        // 0. Normalize Month Year (for Bank Statements)
        if ($request->has('month_year')) {
            try {
                $my = $request->input('month_year');
                // Normalize "April 2026" or "04/2026" to "04-2026"
                $date = Carbon::parse($my);
                $request->merge(['month_year' => $date->format('m-Y')]);
            } catch (\Exception $e) {
                // Fallback: If it's already m-Y, leave it. Carbon::parse handles most cases.
            }
        }

        // 1. Map Category
        if ($request->has('category_name')) {
            $categoryName = $request->input('category_name');
            $category = CustomerCategory::where('name', 'LIKE', $categoryName)
                ->first();
            if (!$category) {
                $category = CustomerCategory::create([
                    'name' => $categoryName
                ]);
            }
            $request->merge(['category_id' => $category->id]);

            // --- AUTO-SYNC TO STANDARD ACCOUNTING CATEGORY ---
            // Determine type based on the request (fallback to Income)
            $type = ($request->input('type') === 'expense') ? 'Expense' : 'Income';

            $stCategory = \App\Models\ProductServiceCategory::where('created_by', $user->creatorId())
                ->where('name', 'LIKE', $categoryName)
                ->where('type', $type)
                ->first();
            
            if (!$stCategory) {
                \App\Models\ProductServiceCategory::create([
                    'name'       => $categoryName,
                    'type'       => $type,
                    'color'      => '#6777ef',
                    'created_by' => $user->creatorId(),
                ]);
            }
        }

        // 2. Map Supplier (Expense)
        if ($request->has('supplier_name')) {
            $supplierName = $request->input('supplier_name');
            $supplier = CustomerSupplier::where('customer_id', $user->id)
                ->where(function($q) use ($supplierName) {
                    $q->where('company_name', 'LIKE', $supplierName)
                      ->orWhere('supplier_name', 'LIKE', $supplierName);
                })->first();

            if (!$supplier) {
                $supplier = CustomerSupplier::create([
                    'customer_id'   => $user->id,
                    'company_name'  => $supplierName,
                    'supplier_name' => $supplierName,
                    'email'         => 'bot_supplier_' . preg_replace('/[^A-Za-z0-9]/', '', $supplierName) . '_' . time() . '@example.com',
                    'postal_code'   => '00000',
                    'city'          => 'Bot City',
                ]);
            }
            $request->merge(['supplier_id' => $supplier->id]);
        }

        // 3. Map Client (Invoice)
        if ($request->has('client_name')) {
            $clientName = $request->input('client_name');
            $client = CustomerClient::where('customer_id', $user->id)
                ->where(function($q) use ($clientName) {
                    $q->where('company_name', 'LIKE', $clientName)
                      ->orWhere('client_name', 'LIKE', $clientName);
                })->first();

            if (!$client) {
                $client = CustomerClient::create([
                    'customer_id'   => $user->id,
                    'company_name'  => $clientName,
                    'client_name'   => $clientName,
                    'email'         => 'bot_client_' . preg_replace('/[^A-Za-z0-9]/', '', $clientName) . '_' . time() . '@example.com',
                    'postal_code'   => '00000',
                    'city'          => 'Bot City',
                ]);
            }
            $request->merge(['client_id' => $client->id]);

            // --- AUTO-SYNC TO STANDARD CUSTOMER ---
            // Ensure the portal has this client as a standard Customer for validation
            $stCustomer = \App\Models\Customer::where('email', $client->email)->first();
            if (!$stCustomer) {
                \App\Models\Customer::create([
                    'name'          => $client->client_name,
                    'email'         => $client->email,
                    'contact'       => $client->telephone,
                    'password'      => \Hash::make('customer_bot_pass'),
                    'billing_city'  => $client->city,
                    'billing_name'  => $client->company_name ?: $client->client_name,
                    'created_by'    => $user->creatorId(),
                    'is_active'     => 1,
                ]);
            }
        }
        
        // 4. Map Tax (TVA) for Articles
        if ($request->has('articles') && is_array($request->input('articles'))) {
            $articles = $request->input('articles');
            foreach ($articles as $key => $article) {
                if (isset($article['tva_percentage'])) {
                    $rate = (float) $article['tva_percentage'];
                    
                    // Look up existing tax by rate
                    $tax = \App\Models\Tax::where('created_by', $user->creatorId())
                        ->where('rate', $rate)
                        ->first();
                        
                    if (!$tax) {
                        $tax = \App\Models\Tax::create([
                            'name'       => 'VAT ' . $rate . '%',
                            'rate'       => $rate,
                            'created_by' => $user->creatorId(),
                        ]);
                    }
                    
                    $articles[$key]['tva_percentage'] = $tax->id;
                }
            }
            $request->merge(['articles' => $articles]);
        }

        // 5. Map Top-level Tax (Fallback for quick invoices)
        $taxInput = $request->input('vat') ?: $request->input('tva_percentage');
        if ($taxInput && is_numeric($taxInput) && strlen($taxInput) < 3) {
            $rate = (float) $taxInput;
            $tax = \App\Models\Tax::where('created_by', $user->creatorId())
                ->where('rate', $rate)
                ->first();

            if (!$tax) {
                $tax = \App\Models\Tax::create([
                    'name'       => 'VAT ' . $rate . '%',
                    'rate'       => $rate,
                    'created_by' => $user->creatorId(),
                ]);
            }
            $request->merge(['tva_percentage' => $tax->id, 'vat' => $tax->id]);
        }

        // 6. Inject Customer ID if missing (for bot requests targeting shared APIs)
        if (!$request->has('customer_id')) {
            $request->merge(['customer_id' => $user->id]);
        }
    }
}
