<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_month_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->unsignedTinyInteger('month');
            $table->year('year');
            $table->enum('status', ['ON_TRACK', 'MISSING_DOCUMENTS', 'IN_REVIEW', 'CLOSED'])->default('MISSING_DOCUMENTS');
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['customer_id', 'month', 'year'], 'unique_workflow_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_month_statuses');
    }
};
