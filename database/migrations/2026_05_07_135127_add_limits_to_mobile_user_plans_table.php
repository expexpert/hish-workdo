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
        Schema::table('mobile_user_plans', function (Blueprint $table) {
            // Adding the new limit columns
            $table->integer('client_limit')->nullable()->after('ocr_limit');
            $table->integer('supplier_limit')->nullable()->after('client_limit');
            $table->boolean('logo')->default(0)->after('supplier_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_user_plans', function (Blueprint $table) {
            $table->dropColumn(['client_limit', 'supplier_limit', 'logo']);
        });
    }
};
