<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\CustomerCategory;
use App\Models\Vender;
use App\Models\CustomerClient;
use App\Models\Tax;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProcessBotInputs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Only run if a user is authenticated (BotAuthMiddleware should have run first)
        if ($user) {
            $this->mapBotInputs($request, $user);
        }

        return $next($request);
    }

    /**
     * Map bot-specific name inputs to database IDs
     */
    protected function mapBotInputs(Request $request, $user)
    {
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
            
            if ($category) {
                $request->merge(['category_id' => $category->id]);
            }
        }

        // 2. Map Supplier (Expense)
        if ($request->has('supplier_name')) {
            $supplierName = $request->input('supplier_name');
            $supplier = Vender::where('customer_id', $user->id)
                ->where(function($q) use ($supplierName) {
                    $q->where('company_name', 'LIKE', $supplierName)
                      ->orWhere('name', 'LIKE', $supplierName);
                })->first();

            // 🛡️ PROTECTION: Do NOT auto-create if we are on the formal "Create Supplier" route
            if (!$supplier && !$request->is('*/customer-supplier')) {
                $supplier = Vender::create([
                    'customer_id'   => $user->id,
                    'company_name'  => $supplierName,
                    'name' => $supplierName,
                    'email'         => 'bot_supplier_' . preg_replace('/[^A-Za-z0-9]/', '', $supplierName) . '_' . time() . '@example.com',
                    'postal_code'   => '00000',
                    'city'          => 'Bot City',
                ]);
            }
            if ($supplier) {
                $request->merge(['supplier_id' => $supplier->id]);
            }
        }

        // 3. Map Client (Invoice)
        if ($request->has('client_name')) {
            $clientName = $request->input('client_name');
            $client = CustomerClient::where('customer_id', $user->id)
                ->where(function($q) use ($clientName) {
                    $q->where('company_name', 'LIKE', $clientName)
                      ->orWhere('client_name', 'LIKE', $clientName);
                })->first();

            // 🛡️ PROTECTION: Do NOT auto-create if we are on the formal "Create Client" route
            if (!$client && !$request->is('*/customer-client')) {
                $client = CustomerClient::create([
                    'customer_id'   => $user->id,
                    'company_name'  => $clientName,
                    'client_name'   => $clientName,
                    'email'         => 'bot_client_' . preg_replace('/[^A-Za-z0-9]/', '', $clientName) . '_' . time() . '@example.com',
                    'postal_code'   => '00000',
                    'city'          => 'Bot City',
                ]);
            }
            
            if ($client) {
                $request->merge(['client_id' => $client->id]);

                // --- AUTO-SYNC TO STANDARD CUSTOMER ---
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
        }
        
        // 4. Map Tax (TVA) for Articles
        if ($request->has('articles') && is_array($request->input('articles'))) {
            $articles = $request->input('articles');
            $company_id = Auth::user() ? Auth::user()->companyId() : null;
            
            if ($company_id) {
                foreach ($articles as $key => $article) {
                    if (isset($article['tva_percentage'])) {
                        $taxId = $article['tva_percentage'];
                        $tax = Tax::where('id', $taxId)
                            ->where('created_by', $company_id)
                            ->first();
                        
                        if (!$tax && is_numeric($taxId)) {
                            $rate = (float) $taxId;
                            $tax = Tax::where('created_by', $company_id)
                                ->where('rate', $rate)
                                ->first();
                            
                            if (!$tax) {
                                $tax = Tax::create([
                                    'name'       => 'VAT ' . $rate . '%',
                                    'rate'       => $rate,
                                    'created_by' => $company_id,
                                ]);
                            }
                            $articles[$key]['tva_percentage'] = $tax->id;
                        }
                    }
                }
                $request->merge(['articles' => $articles]);
            }
        }

        // 5. Top-level Tax (Fallback for quick invoices)
        $taxInput = $request->input('vat') ?: $request->input('tva_percentage');
        $company_id = Auth::user() ? Auth::user()->companyId() : null;
        
        if ($company_id && isset($taxInput) && is_numeric($taxInput)) {
            $tax = Tax::where('id', $taxInput)
                ->where('created_by', $company_id)
                ->first();
            
            if ($tax) {
                $request->merge(['tva_percentage' => $tax->id, 'vat' => $tax->id]);
            } else {
                $rate = (float) $taxInput;
                $tax = Tax::where('created_by', $company_id)
                    ->where('rate', $rate)
                    ->first();

                if (!$tax) {
                    $tax = Tax::create([
                        'name'       => 'VAT ' . $rate . '%',
                        'rate'       => $rate,
                        'created_by' => $company_id,
                    ]);
                }
                $request->merge(['tva_percentage' => $tax->id, 'vat' => $tax->id]);
            }
        }

        // 6. Inject Customer ID if missing
        if (!$request->has('customer_id')) {
            $request->merge(['customer_id' => $user->id]);
        }
    }
}
