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
        Schema::create('client_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['expense', 'revenue']);
            $table->date('transaction_date');
            $table->decimal('amount', 15, 2);

            // Foreign keys for the dropdowns
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('product_service_categories')->onDelete('cascade');

            // Additional fields from your UI
            $table->text('description')->nullable();
            $table->string('reference')->nullable();
            $table->string('attachment_path')->nullable(); // For the Payment Receipt

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_transactions');
    }
};
