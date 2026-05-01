<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referral_codes', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();
            $table->enum('type', ['influencer', 'partner', 'user'])->default('influencer');

            $table->string('owner_name', 150)->nullable();
            $table->foreignId('owner_customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('owner_email')->nullable();

            $table->integer('discount_percentage')->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);

            $table->integer('commission_percentage')->default(0);
            $table->decimal('commission_fixed_amount', 10, 2)->default(0);

            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);

            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();

            $table->boolean('is_active')->default(1);

            $table->timestamps();

            $table->index('code');
            $table->index('type');
            $table->index('owner_customer_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_codes');
    }
};
