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
        Schema::create('referral_conversions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('referral_code_id');
            $table->unsignedBigInteger('referred_customer_id');
            $table->unsignedBigInteger('subscription_id')->nullable();

            $table->decimal('original_price', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);

            $table->string('currency')->default('MAD');
            $table->text('admin_note')->nullable();

            $table->enum('status', ['pending', 'validated', 'rejected', 'paid'])->default('pending');

            $table->dateTime('validated_at')->nullable();
            $table->dateTime('paid_at')->nullable();

            $table->timestamps();

            $table->unique('referred_customer_id');

            $table->foreign('referral_code_id')->references('id')->on('referral_codes')->cascadeOnDelete();
            $table->foreign('referred_customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('subscription_id')->references('id')->on('mobile_user_subscriptions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_conversions');
    }
};
