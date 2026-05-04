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
        Schema::create('mobile_user_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('mobile_user_plan_id');
            $table->unsignedBigInteger('mobile_user_plan_price_id')->nullable();
            $table->unsignedBigInteger('referral_code_id')->nullable();

            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly'])->default('monthly');

            $table->enum('status', [
                'active',
                'pending_payment',
                'past_due',
                'canceled',
                'expired'
            ])->default('pending_payment');

            $table->enum('refund_status', ['none', 'requested', 'processed', 'rejected'])->default('none');
            $table->dateTime('refund_requested_at')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->dateTime('refund_rejected_at')->nullable();
            $table->text('refund_admin_note')->nullable();

            $table->decimal('price_paid', 10, 2)->nullable();
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('referral_discount_amount', 10, 2)->default(0);

            $table->string('currency', 10)->default('MAD');

            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('renews_at')->nullable();
            $table->dateTime('canceled_at')->nullable();

            $table->dateTime('trial_ends_at')->nullable();
            $table->boolean('refund_eligible')->default(1);

            $table->string('payment_provider')->nullable();
            $table->string('provider_customer_id')->nullable();
            $table->string('provider_subscription_id')->nullable();

            $table->timestamps();

            // FKs
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('mobile_user_plan_id')->references('id')->on('mobile_user_plans')->cascadeOnDelete();
            $table->foreign('mobile_user_plan_price_id')->references('id')->on('mobile_user_plan_prices')->nullOnDelete();
            $table->foreign('referral_code_id')->references('id')->on('referral_codes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_user_subscriptions');
    }
};
