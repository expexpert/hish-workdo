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
            $table->text('bio')->nullable()->after('email');

            $table->string('short_bio')->nullable()->after('bio');

            $table->string('ice_number')->nullable()->after('short_bio');
            $table->string('rc_number')->nullable()->after('ice_number');
            $table->string('patent_number')->nullable()->after('rc_number');

            $table->text('address')->nullable()->after('patent_number');

            $table->enum('require_authentication', ['true', 'false'])->default('false')->after('email_verified_at');
            $table->string('vat_number')->nullable()->after('require_authentication');
            $table->string('website')->nullable()->after('vat_number'); 
            $table->timestamp('password_changed_at')->nullable()->after('password');
        });
    }


    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'short_bio',
                'ice_number',
                'rc_number',
                'patent_number',
                'address',
                'require_authentication',
                'vat_number',
                'website',
            ]);
        });
    }
};
