<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Handle customer_quotes
        Schema::table('customer_quotes', function (Blueprint $table) {
            $logicalIndexName = 'customer_quotes_quote_number_unique';

            // Check if index exists before dropping
            if ($this->hasIndex('customer_quotes', $logicalIndexName)) {
                $table->dropUnique($logicalIndexName);
            }
        });

        // Handle customer_invoices
        Schema::table('customer_invoices', function (Blueprint $table) {
            $logicalIndexName = 'customer_invoices_invoice_number_unique';

            if ($this->hasIndex('customer_invoices', $logicalIndexName)) {
                $table->dropUnique($logicalIndexName);
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_quotes', function (Blueprint $table) {
            $table->unique('quote_number');
        });

        Schema::table('customer_invoices', function (Blueprint $table) {
            $table->unique('invoice_number');
        });
    }

    /**
     * Helper to check if an index exists on a table
     */
    private function hasIndex($table, $index): bool
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();

        $results = DB::select(
            "
            SHOW INDEX FROM {$table} 
            WHERE Key_name = ?",
            [$index]
        );

        return count($results) > 0;
    }
};
