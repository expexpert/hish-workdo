<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add to customer_clients
        Schema::table('customer_clients', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        // Add to customer_suppliers
        Schema::table('customer_suppliers', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        // Remove from customer_clients
        Schema::table('customer_clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // Remove from customer_suppliers
        Schema::table('customer_suppliers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
