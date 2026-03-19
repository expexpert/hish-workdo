<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_month_statuses', function (Blueprint $table) {
            // We include the original list + the new 'REJECTED' option
            $table->enum('status', ['ON_TRACK', 'MISSING_DOCUMENTS', 'IN_REVIEW', 'CLOSED', 'REJECTED'])
                ->default('MISSING_DOCUMENTS')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_month_statuses', function (Blueprint $table) {
            // Revert back to the original list
            $table->enum('status', ['ON_TRACK', 'MISSING_DOCUMENTS', 'IN_REVIEW', 'CLOSED'])
                ->default('MISSING_DOCUMENTS')
                ->change();
        });
    }
};
