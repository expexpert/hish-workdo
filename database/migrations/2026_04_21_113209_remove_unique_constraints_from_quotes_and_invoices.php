<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $quoteIndexesToTry = [
            'customer_quotes_quote_number_unique',
            'customer_quotes_invoice_number_unique' // The typo name from your screenshot
        ];

        foreach ($quoteIndexesToTry as $index) {
            if ($this->hasIndex('customer_quotes', $index)) {
                Schema::table('customer_quotes', function (Blueprint $table) use ($index) {
                    $table->dropUnique($index);
                });
            }
        }

        // Handle customer_invoices
        $invoiceIndex = 'customer_invoices_invoice_number_unique';
        if ($this->hasIndex('customer_invoices', $invoiceIndex)) {
            Schema::table('customer_invoices', function (Blueprint $table) use ($invoiceIndex) {
                $table->dropUnique($invoiceIndex);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
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
    private function hasIndex($table, $indexName): bool
    {
        $results = DB::select(
            "SHOW INDEX FROM {$table} WHERE Key_name = ?",
            [$indexName]
        );

        return count($results) > 0;
    }
};
