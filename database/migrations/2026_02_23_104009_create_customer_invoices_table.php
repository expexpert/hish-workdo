<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_invoices', function (Blueprint $table) {
            $table->id();
            
            // Relational field
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained('customer_clients')->onDelete('cascade');
            
            // Fields from the image
            $table->date('date'); // Date
            $table->string('invoice_number')->unique(); // Numéro de facture
            $table->string('payment_method'); // Mode de paiement
            $table->string('status'); // Statut
            
            // Optional: Store the path for "Télécharger un document"
            $table->string('document_path')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_invoices');
    }
};