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
            $table->string('if_number')->nullable()->after('ice_number');
            $table->string('cnss_number')->nullable()->after('if_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venders', function (Blueprint $table) {
            $table->dropColumn(['if_number', 'cnss_number']);
        });
    }
};
