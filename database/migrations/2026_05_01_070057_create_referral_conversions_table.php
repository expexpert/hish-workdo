<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referral_conversions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('referral_code_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referred_customer_id')->constrained('customers')->cascadeOnDelete();

            $table->foreignId('subscription_id')->nullable()
                ->constrained('mobile_user_subscriptions')
                ->nullOnDelete();

            $table->decimal('original_price', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);

            $table->string('currency', 10)->default('MAD');

            $table->enum('status', ['pending', 'validated', 'rejected', 'paid'])->default('pending');

            $table->dateTime('validated_at')->nullable();
            $table->dateTime('paid_at')->nullable();

            $table->timestamps();

            $table->unique('referred_customer_id');
            $table->index('referral_code_id');
            $table->index('subscription_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_conversions');
    }
};
