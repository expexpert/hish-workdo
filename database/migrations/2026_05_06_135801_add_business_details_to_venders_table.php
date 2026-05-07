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
        Schema::table('venders', function (Blueprint $table) {
            // Adding the new columns
            $table->foreignId('customer_id')->nullable()->after('vender_id')->constrained('customers')->onDelete('cascade');
            $table->string('company_name')->nullable()->after('contact');
            $table->string('commercial_register')->nullable()->after('company_name');
            $table->string('ice_number')->nullable()->after('commercial_register');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venders', function (Blueprint $table) {
            // Dropping the columns if the migration is rolled back
            $table->dropColumn(['company_name', 'commercial_register', 'ice_number']);
        });
    }
};
