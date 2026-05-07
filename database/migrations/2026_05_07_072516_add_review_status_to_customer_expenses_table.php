<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_expenses', function (Blueprint $table) {
            // Adding a string column with a default value
            $table->string('review_status')->default('PENDING')->after('notes');
            $table->boolean('bill_status')->default(false)->after('review_status');
        });
    }

    public function down(): void
    {
        Schema::table('customer_expenses', function (Blueprint $table) {
            $table->dropColumn('review_status');
            $table->dropColumn('bill_status');
        });
    }
};
