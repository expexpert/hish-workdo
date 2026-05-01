<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {

            if (!Schema::hasColumn('customers', 'is_b2c')) {
                $table->boolean('is_b2c')->default(0);
            }

            if (!Schema::hasColumn('customers', 'mobile_user_plan_id')) {
                $table->foreignId('mobile_user_plan_id')->nullable()->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('customers', 'storage_used_mb')) {
                $table->integer('storage_used_mb')->default(0);
            }

            if (!Schema::hasColumn('customers', 'app_access_enabled')) {
                $table->boolean('app_access_enabled')->default(1);
            }

            if (!Schema::hasColumn('customers', 'subscription_status')) {
                $table->string('subscription_status')->nullable();
            }

            if (!Schema::hasColumn('customers', 'referral_code_id')) {
                $table->foreignId('referral_code_id')->nullable()->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('customers', 'referral_source')) {
                $table->string('referral_source')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Optional rollback (keep empty to avoid accidental data loss)
    }
};
