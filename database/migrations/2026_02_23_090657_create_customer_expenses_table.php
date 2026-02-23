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
        Schema::create('customer_expenses', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('product_service_categories')->onDelete('cascade');

            // Expense Details
            $table->date('date');
            $table->string('payment_method');
            $table->string('file')->nullable(); // Stores the file path

            // Financials (Using 15,2 for large amounts)
            $table->decimal('ttc', 15, 2);
            $table->decimal('tva', 15, 2)->default(0.00);
            $table->decimal('total_ttc', 15, 2)->nullable();
            $table->decimal('total_tva', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_expenses');
    }
};
