<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarGenerationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_generation', function (Blueprint $table) {
            $table->integer('id')->unsigned()->index()->unique()->startingValue(1)->autoIncrement();
            $table->integer('mark_id')->unsigned()->index();
            $table->integer('model_id')->unsigned()->index();
            $table->string('caption');
            $table->string('code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_generation');
    }
}
