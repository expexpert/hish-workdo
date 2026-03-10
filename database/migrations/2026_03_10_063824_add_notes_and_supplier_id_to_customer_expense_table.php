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
            //
            $table->foreignId('supplier_id')->after('customer_id')->nullable()->constrained('customer_suppliers')->onDelete('set null');
            $table->text('notes')->nullable()->after('total_tva');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_expenses', function (Blueprint $table) {
            //
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'notes']);
        });
    }
};
