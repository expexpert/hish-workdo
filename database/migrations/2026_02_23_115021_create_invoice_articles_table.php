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
        Schema::create('invoice_articles', function (Blueprint $table) {
            $table->id();
            
            // Link to the main invoice
            $table->foreignId('invoice_id')->constrained('customer_invoices')->onDelete('cascade');
            
            // Fields from the image
            $table->string('designation'); // Désignation
            $table->decimal('unit_price_ht', 15, 2); // Prix H.T. unitaire
            $table->integer('quantity'); // Quantité
            $table->decimal('total_price_ht', 15, 2); // Prix H.T. total
            $table->decimal('tva_percentage', 5, 2); // T.V.A. %

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_articles');
    }
};