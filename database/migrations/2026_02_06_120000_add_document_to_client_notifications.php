<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('client_notifications')) {
            Schema::table('client_notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('client_notifications', 'document')) {
                    $table->string('document')->nullable()->after('data');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('client_notifications')) {
            Schema::table('client_notifications', function (Blueprint $table) {
                if (Schema::hasColumn('client_notifications', 'document')) {
                    $table->dropColumn('document');
                }
            });
        }
    }
};
