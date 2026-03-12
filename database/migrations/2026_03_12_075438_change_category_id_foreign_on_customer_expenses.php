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
            // 1. Drop the old foreign key
            $table->dropForeign(['category_id']);

            // 2. Re-assign it to the new table
            $table->foreign('category_id')
                ->references('id')
                ->on('customer_categories')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('customer_expenses', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->foreign('category_id')
                ->references('id')
                ->on('product_service_categories')
                ->onDelete('cascade');
        });
    }
};
