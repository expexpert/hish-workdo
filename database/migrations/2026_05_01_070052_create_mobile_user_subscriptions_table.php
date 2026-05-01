<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mobile_user_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mobile_user_plan_id')->constrained()->cascadeOnDelete();

            $table->foreignId('mobile_user_plan_price_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('referral_code_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly'])->default('monthly');

            $table->enum('status', [
                'trialing',
                'active',
                'pending_payment',
                'past_due',
                'canceled',
                'expired'
            ])->default('active');

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

            $table->string('payment_provider', 50)->nullable();
            $table->string('provider_customer_id')->nullable();
            $table->string('provider_subscription_id')->nullable();

            $table->timestamps();

            $table->index('customer_id');
            $table->index('mobile_user_plan_id');
            $table->index('mobile_user_plan_price_id');
            $table->index('referral_code_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_user_subscriptions');
    }
};
