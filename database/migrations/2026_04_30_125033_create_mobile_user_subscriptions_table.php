<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mobile_user_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('mobile_user_plan_id');

            $table->enum('status', [
                'trialing',
                'active',
                'past_due',
                'canceled',
                'expired'
            ])->default('active');

            $table->dateTime('starts_at')->nullable();
            $table->dateTime('trial_ends_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('renews_at')->nullable();
            $table->dateTime('canceled_at')->nullable();            
            $table->boolean('refund_eligible')->default(true);
            $table->string('payment_provider', 50)->nullable();
            $table->string('provider_customer_id')->nullable();
            $table->string('provider_subscription_id')->nullable();

            $table->timestamps();

            $table->index('customer_id');
            $table->index('mobile_user_plan_id');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->cascadeOnDelete();

            $table->foreign('mobile_user_plan_id')
                ->references('id')
                ->on('mobile_user_plans')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_user_subscriptions');
    }
};
