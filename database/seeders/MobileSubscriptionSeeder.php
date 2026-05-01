<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MobileUserPlan;
use App\Models\MobileUserPlanPrice;
use App\Models\ReferralCode;

class MobileSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // PLANS
        // =========================
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'invoice_limit' => 5,
                'quote_limit' => 5,
                'expense_limit' => 20,
                'receipt_limit' => 20,
                'ocr_limit' => 5,
                'storage_limit_mb' => 100,
                'export_enabled' => 0,
                'whatsapp_bot_enabled' => 0,
                'is_active' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'invoice_limit' => 30,
                'quote_limit' => 30,
                'expense_limit' => 100,
                'receipt_limit' => 100,
                'ocr_limit' => 30,
                'storage_limit_mb' => 500,
                'export_enabled' => 1,
                'whatsapp_bot_enabled' => 0,
                'is_active' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'invoice_limit' => null,
                'quote_limit' => null,
                'expense_limit' => null,
                'receipt_limit' => null,
                'ocr_limit' => 150,
                'storage_limit_mb' => 2048,
                'export_enabled' => 1,
                'whatsapp_bot_enabled' => 0,
                'is_active' => 1,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'invoice_limit' => null,
                'quote_limit' => null,
                'expense_limit' => null,
                'receipt_limit' => null,
                'ocr_limit' => 500,
                'storage_limit_mb' => 10240,
                'export_enabled' => 1,
                'whatsapp_bot_enabled' => 1,
                'is_active' => 1,
            ],
        ];

        foreach ($plans as $planData) {

            $plan = MobileUserPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );

            $this->seedPrices($plan);
        }

        // =========================
        // REFERRAL CODE
        // =========================
        ReferralCode::updateOrCreate(
            ['code' => 'YASSINE10'],
            [
                'type' => 'influencer',
                'owner_name' => 'Yassine Influencer',
                'owner_email' => 'yassine@example.com',
                'discount_percentage' => 10,
                'discount_amount' => 0,
                'commission_percentage' => 20,
                'commission_fixed_amount' => 0,
                'max_uses' => null,
                'used_count' => 0,
                'is_active' => 1,
            ]
        );
    }

    private function seedPrices($plan)
    {
        $prices = [];

        switch ($plan->slug) {

            case 'free':
                $prices = [
                    ['cycle' => 'monthly', 'price' => 0, 'discount' => 0],
                ];
                break;

            case 'basic':
                $prices = [
                    ['cycle' => 'monthly', 'price' => 49, 'discount' => 0],
                    ['cycle' => 'quarterly', 'price' => 139, 'discount' => 5],
                    ['cycle' => 'yearly', 'price' => 499, 'discount' => 15],
                ];
                break;

            case 'pro':
                $prices = [
                    ['cycle' => 'monthly', 'price' => 99, 'discount' => 0],
                    ['cycle' => 'quarterly', 'price' => 279, 'discount' => 6],
                    ['cycle' => 'yearly', 'price' => 999, 'discount' => 16],
                ];
                break;

            case 'business':
                $prices = [
                    ['cycle' => 'monthly', 'price' => 199, 'discount' => 0],
                    ['cycle' => 'quarterly', 'price' => 549, 'discount' => 8],
                    ['cycle' => 'yearly', 'price' => 1999, 'discount' => 16],
                ];
                break;
        }

        foreach ($prices as $price) {

            MobileUserPlanPrice::updateOrCreate(
                [
                    'mobile_user_plan_id' => $plan->id,
                    'billing_cycle' => $price['cycle'],
                ],
                [
                    'price' => $price['price'],
                    'currency' => 'MAD',
                    'discount_percentage' => $price['discount'],
                    'is_active' => 1,
                ]
            );
        }
    }
}
