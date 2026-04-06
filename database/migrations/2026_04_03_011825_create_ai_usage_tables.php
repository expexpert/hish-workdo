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
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('channel')->default('whatsapp');
            $table->string('model');
            $table->integer('tokens_in')->default(0);
            $table->integer('tokens_out')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->decimal('estimated_cost', 10, 6)->default(0.000000);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('customers')->onDelete('cascade');
        });

        Schema::create('ai_user_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->integer('daily_request_limit')->default(30);
            $table->integer('monthly_token_limit')->default(25000);
            $table->timestamp('last_request_at')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_user_limits');
        Schema::dropIfExists('ai_usage_logs');
    }
};
