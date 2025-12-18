<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepreciationTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('depreciation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('assetid')->nullable();
            $table->integer('componentid')->nullable();
            $table->string('period');
            $table->string('assetvalue');
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
        Schema::dropIfExists('depreciation');
    }
}
