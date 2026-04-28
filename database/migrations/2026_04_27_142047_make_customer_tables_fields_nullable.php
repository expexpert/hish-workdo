<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // customer_suppliers table
        Schema::table('customer_suppliers', function (Blueprint $table) {
            $table->string('postal_code')->nullable()->change();
            $table->string('city')->nullable()->change();
        });

        // customer_clients table
        Schema::table('customer_clients', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
            $table->string('city')->nullable()->change();
        });
    }

    public function down(): void
    {
        // revert customer_suppliers
        Schema::table('customer_suppliers', function (Blueprint $table) {
            $table->string('postal_code')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
        });

        // revert customer_clients
        Schema::table('customer_clients', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('postal_code')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
        });
    }
};
