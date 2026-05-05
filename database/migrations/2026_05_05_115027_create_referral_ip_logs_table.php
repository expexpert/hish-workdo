<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referral_ip_logs', function (Blueprint $table) {
            $table->id();
            $table->string('referral_code');
            $table->string('ip_address', 45); // supports IPv6
            $table->boolean('is_used')->default(false);
            $table->timestamps();

            // indexes for faster lookup
            $table->index(['referral_code', 'ip_address']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_ip_logs');
    }
};
