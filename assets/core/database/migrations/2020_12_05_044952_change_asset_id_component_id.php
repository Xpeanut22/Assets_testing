<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAssetIdComponentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('component_assets', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('assetid')->change();
            $table->unsignedBigInteger('componentid')->change();

            $table->foreign('assetid')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('componentid')->references('id')->on('component')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('component_assets', function (Blueprint $table) {
            //
        });
    }
}
