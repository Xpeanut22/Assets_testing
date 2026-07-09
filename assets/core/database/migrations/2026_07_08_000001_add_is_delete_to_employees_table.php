<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDeleteToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('employees', 'is_delete')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->tinyInteger('is_delete')->default(0)->after('address');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('employees', 'is_delete')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('is_delete');
            });
        }
    }
}
