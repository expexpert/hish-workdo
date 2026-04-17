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
        Schema::table('invoice_articles', function (Blueprint $table) {
            $table->unsignedBigInteger('tva_percentage')->nullable()->change();
        });

        Schema::table('quotes_articles', function (Blueprint $table) {
            $table->unsignedBigInteger('tva_percentage')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_articles', function (Blueprint $table) {
            $table->decimal('tva_percentage', 5, 2)->nullable(false)->change();
        });

        Schema::table('quotes_articles', function (Blueprint $table) {
            $table->decimal('tva_percentage', 5, 2)->nullable(false)->change();
        });
    }
};
