<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('mobile_user_plan_prices', function (Blueprint $table) {
            $table->dropUnique('mobile_user_plan_prices_mobile_user_plan_id_billing_cycle_unique');
        });
    }

    public function down()
    {
        Schema::table('mobile_user_plan_prices', function (Blueprint $table) {
            $table->unique(['mobile_user_plan_id', 'billing_cycle']);
        });
    }
};
