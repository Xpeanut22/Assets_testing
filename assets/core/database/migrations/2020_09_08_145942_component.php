<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Component extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('component', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supplierid');
            $table->integer('typeid');
            $table->integer('brandid');
            $table->string('name',255);
            $table->string('serial',255);
            $table->string('quantity',10);
            $table->date('purchasedate');
            $table->string('cost',10);
            $table->string('warranty',5);
            $table->string('status',20);
            $table->text('picture')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists( 'component' );
    }
}
