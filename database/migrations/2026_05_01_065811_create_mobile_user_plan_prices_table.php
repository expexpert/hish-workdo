<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mobile_user_plan_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mobile_user_plan_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly']);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 10)->default('MAD');
            $table->integer('discount_percentage')->default(0);
            $table->boolean('is_active')->default(1);

            $table->timestamps();

            $table->unique(['mobile_user_plan_id', 'billing_cycle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_user_plan_prices');
    }
};
