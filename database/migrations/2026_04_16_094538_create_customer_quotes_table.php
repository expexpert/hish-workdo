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
        Schema::create('customer_quotes', function (Blueprint $table) {
            $table->id();

            // Relational field
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained('customer_clients')->onDelete('cascade');

            // Fields from the image
            $table->date('date');
            $table->date('due_date');
            $table->string('quote_number')->unique();
            $table->string('payment_method');
            $table->string('status');
            $table->string('review_status')->nullable();
            $table->string('notes')->nullable();
            $table->string('document_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_quotes');
    }
};
