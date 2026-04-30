<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MobileUserPlansSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mobile_user_plans')->insert([
            [
                'name' => 'Free',
                'slug' => 'free',
                'price_monthly' => 0,
                'invoice_limit' => 5,
                'quote_limit' => 5,
                'expense_limit' => 20,
                'receipt_limit' => 20,
                'ocr_limit' => 5,
                'storage_limit_mb' => 100,
                'export_enabled' => 0,
                'whatsapp_bot_enabled' => 0,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price_monthly' => 49,
                'invoice_limit' => 30,
                'quote_limit' => 30,
                'expense_limit' => 100,
                'receipt_limit' => 100,
                'ocr_limit' => 30,
                'storage_limit_mb' => 500,
                'export_enabled' => 1,
                'whatsapp_bot_enabled' => 0,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price_monthly' => 99,
                'invoice_limit' => null,
                'quote_limit' => null,
                'expense_limit' => null,
                'receipt_limit' => null,
                'ocr_limit' => 150,
                'storage_limit_mb' => 2048,
                'export_enabled' => 1,
                'whatsapp_bot_enabled' => 0,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'price_monthly' => 199,
                'invoice_limit' => null,
                'quote_limit' => null,
                'expense_limit' => null,
                'receipt_limit' => null,
                'ocr_limit' => 500,
                'storage_limit_mb' => 10240,
                'export_enabled' => 1,
                'whatsapp_bot_enabled' => 1,
            ],
        ]);
    }
}
