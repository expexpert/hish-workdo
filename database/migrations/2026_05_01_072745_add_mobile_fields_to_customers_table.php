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
        Schema::table('customers', function (Blueprint $table) {

            $table->boolean('is_b2c')->default(0)->after('tax_number');
            $table->unsignedBigInteger('mobile_user_plan_id')->nullable()->after('is_b2c');
            $table->integer('storage_used_mb')->default(0)->after('mobile_user_plan_id');
            $table->boolean('app_access_enabled')->default(1)->after('storage_used_mb');
            $table->string('subscription_status')->nullable()->after('app_access_enabled');
            $table->unsignedBigInteger('referral_code_id')->nullable()->after('subscription_status');
            $table->string('referral_source')->nullable()->after('referral_code_id');

            $table->foreign('mobile_user_plan_id')
                ->references('id')->on('mobile_user_plans')
                ->nullOnDelete();

            $table->foreign('referral_code_id')
                ->references('id')->on('referral_codes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
};
