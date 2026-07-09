<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AssetHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('assetid');
            $table->integer('employeeid');
            $table->date('date');
            $table->string('status', 50);
            $table->integer('typeofid')->nullable();
            $table->integer('depid')->nullable();
            $table->string('condition', 100)->nullable();
            $table->string('used', 100)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('control_number', 191)->nullable();
            $table->text('remarks')->nullable();
            $table->integer('groupid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'asset_history' );
    }
}
