<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mobile_user_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->string('currency', 10)->default('MAD');

            $table->integer('invoice_limit')->nullable();
            $table->integer('quote_limit')->nullable();
            $table->integer('expense_limit')->nullable();
            $table->integer('receipt_limit')->nullable();
            $table->integer('ocr_limit')->nullable();

            $table->integer('storage_limit_mb')->default(500);

            $table->boolean('export_enabled')->default(false);
            $table->boolean('whatsapp_bot_enabled')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_user_plans');
    }
};
