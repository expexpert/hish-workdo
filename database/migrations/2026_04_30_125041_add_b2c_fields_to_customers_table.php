<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_b2c')->default(false)->after('tax_number');

            $table->unsignedBigInteger('mobile_user_plan_id')->nullable()->after('is_b2c');

            $table->integer('storage_used_mb')->default(0)->after('mobile_user_plan_id');

            $table->boolean('app_access_enabled')->default(true)->after('storage_used_mb');

            $table->string('subscription_status', 50)->nullable()->after('app_access_enabled');

            // Optional FK
            $table->foreign('mobile_user_plan_id')
                ->references('id')
                ->on('mobile_user_plans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['mobile_user_plan_id']);

            $table->dropColumn([
                'is_b2c',
                'mobile_user_plan_id',
                'storage_used_mb',
                'app_access_enabled',
                'subscription_status'
            ]);
        });
    }
};
