<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car', function($table) {
            $table->foreign('mark_id')->references('id')->on('car_mark');
            $table->foreign('model_id')->references('id')->on('car_model');
            $table->foreign('model_generation_id')->references('id')->on('car_generation');
            $table->foreign('color_id')->references('id')->on('car_color');
            $table->foreign('engine_type_id')->references('id')->on('car_engine_type');
            $table->foreign('transmition_type_id')->references('id')->on('car_transmition_type');
            $table->foreign('gear_type_id')->references('id')->on('car_gear_type');
            $table->foreign('body_type_id')->references('id')->on('car_body_type');
        });
        Schema::table('car_model', function($table) {
            $table->foreign('mark_id')->references('id')->on('car_mark');
        });
        Schema::table('car_generation', function($table) {
            $table->foreign('mark_id')->references('id')->on('car_mark');
            $table->foreign('model_id')->references('id')->on('car_model');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_generation', function($table) {
            $table->dropforeign(['mark_id']);
            $table->dropforeign(['model_id']);
        });
        Schema::table('car_model', function($table) {
            $table->dropforeign(['mark_id']);
        });
        Schema::table('car', function($table) {
            $table->dropforeign(['mark_id']);
            $table->dropforeign(['model_id']);
            $table->dropforeign(['model_generation_id']);
            $table->dropforeign(['color_id']);
            $table->dropforeign(['engine_type_id']);
            $table->dropforeign(['transmition_type_id']);
            $table->dropforeign(['gear_type_id']);
            $table->dropforeign(['body_type_id']);
        });
    }
}
