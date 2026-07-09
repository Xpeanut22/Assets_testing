<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDeleteToMainTables extends Migration
{
    private $tables = [
        'users',
        'assets',
        'component',
        'brand',
        'supplier',
        'location',
        'department',
        'asset_type',
        'category',
        'units',
        'used',
        'receiver',
        'maintenance',
        'depreciation',
        'file',
        'typeofid',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'is_delete')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->tinyInteger('is_delete')->default(0);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'is_delete')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('is_delete');
                });
            }
        }
    }
}
