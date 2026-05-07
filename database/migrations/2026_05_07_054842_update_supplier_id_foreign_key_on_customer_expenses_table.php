<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE customer_expenses DROP FOREIGN KEY customer_expenses_supplier_id_foreign');
        } catch (\Exception $e) {
            // Ignore error: the key simply doesn't exist.
        }

        // 2. Now that the "phantom" key is handled, use standard Schema to link to venders.
        Schema::table('customer_expenses', function (Blueprint $table) {
            $table->foreign('supplier_id')
                ->references('id')
                ->on('venders')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('customer_expenses', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->foreign('supplier_id')
                ->references('id')
                ->on('customer_suppliers')
                ->onDelete('set null');
        });
    }
};
