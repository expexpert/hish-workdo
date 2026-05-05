<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'customer_invoices',
        'customer_expenses',
        'customer_quotes',
        'revenues',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasColumn($table, 'is_ocr')) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    if (Schema::hasColumn($tableBlueprint->getTable(), 'created_at')) {
                        $tableBlueprint->boolean('is_ocr')
                            ->default(false)
                            ->before('created_at');
                    } else {
                        $tableBlueprint->boolean('is_ocr')
                            ->default(false);
                    }
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'is_ocr')) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->dropColumn('is_ocr');
                });
            }
        }
    }
};
