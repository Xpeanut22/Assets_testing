<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ComponentAssets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('component_assets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('assetid');
            $table->unsignedBigInteger('componentid');
            $table->integer('quantity');
            $table->string('status', 50);
            $table->integer('employeeid')->nullable();
            $table->string('contactno', 100)->nullable();
            $table->integer('typeofid')->nullable();
            $table->string('idno', 100)->nullable();
            $table->unsignedBigInteger('department')->nullable();
            $table->date('date');
            $table->string('control_number', 191)->nullable();
            $table->string('issuancetype', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists( 'component_assets' );
    }
}
