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
        Schema::table('customer_expenses', function (Blueprint $table) {
            // 1. Drop the old foreign key constraint
            $table->dropForeign(['supplier_id']);

            // 2. Change the constraint to point to the 'venders' table
            $table->foreign('supplier_id')
                ->references('id')
                ->on('venders')
                ->onDelete('set null')
                ->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_expenses', function (Blueprint $table) {
            // 1. Drop the new vendor constraint
            $table->dropForeign(['supplier_id']);

            // 2. Restore the original constraint to 'customer_suppliers'
            $table->foreign('supplier_id')
                ->references('id')
                ->on('customer_suppliers')
                ->onDelete('set null')
                ->change();
        });
    }
};