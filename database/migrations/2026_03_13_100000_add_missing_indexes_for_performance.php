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
        Schema::table('customer_invoices', function (Blueprint $table) {
            $table->index(['customer_id', 'date']);
            $table->index(['customer_id', 'status']);
        });

        Schema::table('customer_expenses', function (Blueprint $table) {
            $table->index(['customer_id', 'date']);
            $table->index(['customer_id', 'category_id']);
        });

        Schema::table('client_notifications', function (Blueprint $table) {
            $table->index(['customer_id', 'is_read']);
        });

        Schema::table('client_transactions', function (Blueprint $table) {
            $table->index(['customer_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_invoices', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'date']);
            $table->dropIndex(['customer_id', 'status']);
        });

        Schema::table('customer_expenses', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'date']);
            $table->dropIndex(['customer_id', 'category_id']);
        });

        Schema::table('client_notifications', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'is_read']);
        });

        Schema::table('client_transactions', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'transaction_date']);
        });
    }
};
