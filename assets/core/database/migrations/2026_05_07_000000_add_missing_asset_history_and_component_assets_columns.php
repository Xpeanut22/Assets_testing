<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingAssetHistoryAndComponentAssetsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asset_history', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_history', 'typeofid')) {
                $table->integer('typeofid')->nullable()->after('status');
            }
            if (!Schema::hasColumn('asset_history', 'depid')) {
                $table->integer('depid')->nullable()->after('typeofid');
            }
            if (!Schema::hasColumn('asset_history', 'condition')) {
                $table->string('condition', 100)->nullable()->after('depid');
            }
            if (!Schema::hasColumn('asset_history', 'used')) {
                $table->string('used', 100)->nullable()->after('condition');
            }
            if (!Schema::hasColumn('asset_history', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('used');
            }
            if (!Schema::hasColumn('asset_history', 'control_number')) {
                $table->string('control_number', 191)->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('asset_history', 'remarks')) {
                $table->text('remarks')->nullable()->after('control_number');
            }
            if (!Schema::hasColumn('asset_history', 'groupid')) {
                $table->integer('groupid')->nullable()->after('remarks');
            }
        });

        Schema::table('component_assets', function (Blueprint $table) {
            if (!Schema::hasColumn('component_assets', 'status')) {
                $table->string('status', 50)->after('quantity');
            }
            if (!Schema::hasColumn('component_assets', 'employeeid')) {
                $table->integer('employeeid')->nullable()->after('status');
            }
            if (!Schema::hasColumn('component_assets', 'contactno')) {
                $table->string('contactno', 100)->nullable()->after('employeeid');
            }
            if (!Schema::hasColumn('component_assets', 'typeofid')) {
                $table->integer('typeofid')->nullable()->after('contactno');
            }
            if (!Schema::hasColumn('component_assets', 'idno')) {
                $table->string('idno', 100)->nullable()->after('typeofid');
            }
            if (!Schema::hasColumn('component_assets', 'department')) {
                $table->unsignedBigInteger('department')->nullable()->after('idno');
            }
            if (!Schema::hasColumn('component_assets', 'control_number')) {
                $table->string('control_number', 191)->nullable()->after('department');
            }
            if (!Schema::hasColumn('component_assets', 'issuancetype')) {
                $table->string('issuancetype', 100)->nullable()->after('control_number');
            }
            if (!Schema::hasColumn('component_assets', 'remarks')) {
                $table->text('remarks')->nullable()->after('issuancetype');
            }
            if (!Schema::hasColumn('component_assets', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('remarks');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asset_history', function (Blueprint $table) {
            if (Schema::hasColumn('asset_history', 'groupid')) {
                $table->dropColumn('groupid');
            }
            if (Schema::hasColumn('asset_history', 'remarks')) {
                $table->dropColumn('remarks');
            }
            if (Schema::hasColumn('asset_history', 'control_number')) {
                $table->dropColumn('control_number');
            }
            if (Schema::hasColumn('asset_history', 'created_by')) {
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('asset_history', 'used')) {
                $table->dropColumn('used');
            }
            if (Schema::hasColumn('asset_history', 'condition')) {
                $table->dropColumn('condition');
            }
            if (Schema::hasColumn('asset_history', 'depid')) {
                $table->dropColumn('depid');
            }
            if (Schema::hasColumn('asset_history', 'typeofid')) {
                $table->dropColumn('typeofid');
            }
        });

        Schema::table('component_assets', function (Blueprint $table) {
            if (Schema::hasColumn('component_assets', 'created_by')) {
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('component_assets', 'remarks')) {
                $table->dropColumn('remarks');
            }
            if (Schema::hasColumn('component_assets', 'issuancetype')) {
                $table->dropColumn('issuancetype');
            }
            if (Schema::hasColumn('component_assets', 'control_number')) {
                $table->dropColumn('control_number');
            }
            if (Schema::hasColumn('component_assets', 'department')) {
                $table->dropColumn('department');
            }
            if (Schema::hasColumn('component_assets', 'idno')) {
                $table->dropColumn('idno');
            }
            if (Schema::hasColumn('component_assets', 'typeofid')) {
                $table->dropColumn('typeofid');
            }
            if (Schema::hasColumn('component_assets', 'contactno')) {
                $table->dropColumn('contactno');
            }
            if (Schema::hasColumn('component_assets', 'employeeid')) {
                $table->dropColumn('employeeid');
            }
            if (Schema::hasColumn('component_assets', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
}
