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
        Schema::table('customers_products', function (Blueprint $table) {
            $table->text('description')->nullable()->after('id');

            $table->string('reference')->nullable()->after('description');
            $table->string('category')->nullable()->after('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers_products', function (Blueprint $table) {
            $table->dropColumn(['description', 'reference', 'category']);
        });
    }
};
