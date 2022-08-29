<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('car_id');
            $table->bigInteger('generation_id');
            $table->integer('mark_id')->unsigned()->index();
            $table->integer('model_id')->unsigned()->index();
            $table->integer('model_generation_id')->unsigned()->index();
            $table->integer('year');
            $table->integer('run');
            $table->integer('color_id')->unsigned()->index();
            $table->integer('engine_type_id')->unsigned()->index();
            $table->integer('transmition_type_id')->unsigned()->index();
            $table->integer('gear_type_id')->unsigned()->index();
            $table->integer('body_type_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car');
    }
}
