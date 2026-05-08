<?php

namespace App\Http\Controllers;

use App\Models\MobileUserPlan;
use App\Models\MobileUserPlanPrice;
use App\Models\MobileUserSubscription;
use App\Models\User;
use App\Models\Utility;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MobilePlanController extends Controller
{

    public function index()
    {
        if (Auth::user()->can('manage plan')) {
            if (\Auth::user()->type == 'super admin') {
                $mobilePlans = MobileUserPlan::get();
                return view('MobilePlan.index', compact('mobilePlans'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (Auth::user()->can('create plan')) {
            return view('MobilePlan._form');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if (!\Auth::user()->can('create plan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'name'             => 'required|unique:mobile_user_plans,name',
            'slug'             => 'nullable|unique:mobile_user_plans,slug',
            'invoice_limit'    => 'nullable|numeric',
            'quote_limit'      => 'nullable|numeric',
            'expense_limit'    => 'nullable|numeric',
            'receipt_limit'    => 'nullable|numeric',
            'ocr_limit'        => 'nullable|numeric',
            'storage_limit_mb' => 'required|numeric',
            'client_limit' => 'required|numeric',
            'supplier_limit' => 'required|numeric',
            'prices'                       => 'required|array|min:1',
            'prices.*.billing_cycle'       => 'required|string',
            'prices.*.price'               => 'required|numeric|min:0',
            'prices.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        \DB::beginTransaction();

        try {
            $plan = MobileUserPlan::create([
                'name'                 => $request->name,
                'slug'                 => $request->slug ?? \Str::slug($request->name),
                'invoice_limit'        => $request->invoice_limit,
                'quote_limit'          => $request->quote_limit,
                'expense_limit'        => $request->expense_limit,
                'receipt_limit'        => $request->receipt_limit,
                'ocr_limit'            => $request->ocr_limit,
                'storage_limit_mb'     => $request->storage_limit_mb,
                'client_limit'       => $request->client_limit,
                'supplier_limit'       => $request->supplier_limit,
                'export_enabled'       => $request->has('export_enabled'),
                'whatsapp_bot_enabled' => $request->has('whatsapp_bot_enabled'),
                'logo'                 => $request->has('logo'),
            ]);

            foreach (array_values($request->prices) as $index => $priceData) {

                $plan->prices()->create([
                    'billing_cycle'       => $priceData['billing_cycle'],
                    'price'               => $priceData['price'],
                    'discount_percentage' => $priceData['discount_percentage'] ?? 0,
                ]);
            }

            \DB::commit();

            return redirect()->back()->with('success', __('Plan successfully created.'));
        } catch (\Exception $e) {
            \DB::rollBack();

            return redirect()->back()->with(
                'error',
                __('Failed to create plan: ') . $e->getMessage()
            );
        }
    }


    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit plan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $plan = MobileUserPlan::with('prices')->findOrFail($id);

        $request->validate([
            'name'             => 'required|unique:mobile_user_plans,name,' . $plan->id,
            'slug'             => 'nullable|unique:mobile_user_plans,slug,' . $plan->id,
            'invoice_limit'    => 'nullable|numeric',
            'quote_limit'      => 'nullable|numeric',
            'expense_limit'    => 'nullable|numeric',
            'receipt_limit'    => 'nullable|numeric',
            'ocr_limit'        => 'nullable|numeric',
            'storage_limit_mb' => 'required|numeric',
            'client_limit' => 'required|numeric',
            'supplier_limit' => 'required|numeric',
            'prices'                       => 'required|array|min:1',
            'prices.*.billing_cycle'       => 'required|string',
            'prices.*.price'               => 'required|numeric|min:0',
            'prices.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        \DB::beginTransaction();

        try {
            $plan->update([
                'name'                 => $request->name,
                'slug'                 => $request->slug ?? \Str::slug($request->name),
                'invoice_limit'        => $request->invoice_limit,
                'quote_limit'          => $request->quote_limit,
                'expense_limit'        => $request->expense_limit,
                'receipt_limit'        => $request->receipt_limit,
                'ocr_limit'            => $request->ocr_limit,
                'storage_limit_mb'     => $request->storage_limit_mb,
                'client_limit'      => $request->client_limit,
                'supplier_limit'       => $request->supplier_limit,
                'export_enabled'       => $request->has('export_enabled'),
                'whatsapp_bot_enabled' => $request->has('whatsapp_bot_enabled'),
                'logo'                 => $request->has('logo'),
            ]);

            $existingIds = $plan->prices->pluck('id')->toArray();
            $updatedIds  = [];

            foreach (array_values($request->prices) as $priceData) {

                // Skip completely empty rows
                if (empty($priceData['billing_cycle']) || $priceData['price'] === '') {
                    continue;
                }

                if (!empty($priceData['id'])) {
                    // ✅ Existing price — update it
                    $price = MobileUserPlanPrice::find($priceData['id']);

                    if ($price) {
                        $price->update([
                            'billing_cycle'       => $priceData['billing_cycle'],
                            'price'               => $priceData['price'],
                            'discount_percentage' => $priceData['discount_percentage'] ?? 0,
                        ]);

                        $updatedIds[] = $price->id;
                    }
                } else {
                    // ✅ New price row — create it
                    $newPrice = $plan->prices()->create([
                        'billing_cycle'       => $priceData['billing_cycle'],
                        'price'               => $priceData['price'],
                        'discount_percentage' => $priceData['discount_percentage'] ?? 0,
                    ]);

                    $updatedIds[] = $newPrice->id;
                }
            }

            // ✅ Delete prices that were removed in the form
            $toDelete = array_diff($existingIds, $updatedIds);

            if (!empty($toDelete)) {
                MobileUserPlanPrice::whereIn('id', $toDelete)->delete();
            }

            \DB::commit();

            return redirect()->back()->with('success', __('Plan updated successfully.'));
        } catch (\Exception $e) {
            \DB::rollBack();

            return redirect()->back()->with(
                'error',
                __('Failed to update plan: ') . $e->getMessage()
            );
        }
    }

    public function edit($plan_id)
    {
        if (Auth::user()->can('edit plan')) {
            $plan        = MobileUserPlan::find($plan_id);

            return view('MobilePlan._form', compact('plan'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(Request $request, $id)
    {
        $plan = MobileUserPlan::find($id);
        if ($plan->id == $id) {
            MobileUserPlanPrice::where('mobile_user_plan_id', $id)->delete();
            $plan->delete();

            return redirect()->back()->with('success', __('Plan deleted successfully'));
        } else {
            return redirect()->back()->with('error', __('Something went wrong'));
        }
    }


    public function MobileSubscription(Request $request)
    {
        $mobileSubscriptions = MobileUserSubscription::with('plan', 'price', 'customer', 'referralCode')->get();
        return view('MobilePlan.subscription', compact('mobileSubscriptions'));
    }
}
