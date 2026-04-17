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
        Schema::create('quotes_articles', function (Blueprint $table) {
            $table->id();

            // Link to the main invoice
            $table->foreignId('quotes_id')->constrained('customer_quotes')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();

            $table->string('designation');
            $table->decimal('unit_price_ht', 15, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total_price_ht', 15, 2);
            $table->unsignedBigInteger('tva_percentage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes_articles');
    }
};
