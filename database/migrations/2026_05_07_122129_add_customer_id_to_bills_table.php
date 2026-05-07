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
        Schema::table('bills', function (Blueprint $table) {
            // This adds a bigInteger column and sets the foreign key in one go
            $table->foreignId('customer_id')
                ->after('vender_id') // Optional: places it after the 'id' column
                ->constrained('customers') // References 'id' on 'customers' table
                ->onDelete('cascade'); // Optional: deletes bills if customer is deleted
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            // Drop the foreign key first, then the column
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
