<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mobile_user_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();

            $table->integer('invoice_limit')->nullable();
            $table->integer('quote_limit')->nullable();
            $table->integer('expense_limit')->nullable();
            $table->integer('receipt_limit')->nullable();
            $table->integer('ocr_limit')->nullable();

            $table->integer('storage_limit_mb')->default(500);
            $table->boolean('export_enabled')->default(0);
            $table->boolean('whatsapp_bot_enabled')->default(0);
            $table->boolean('is_active')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_user_plans');
    }
};
