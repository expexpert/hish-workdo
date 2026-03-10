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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('signature')->nullable()->aftar('avatar');
            $table->string('if_number')->nullable()->after('patent_number');
            $table->string('cnss')->nullable()->after('if_number');

            $table->string('company_type')->nullable()->after('cnss');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            //
            $table->dropColumn(['signature', 'if_number', 'cnss', 'company_type']);
        });
    }
};
