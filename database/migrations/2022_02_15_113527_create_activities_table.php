<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->tinyInteger('is_done');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
            $table->bigInteger('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade'); 
            $table->text('before_photo_url');
            $table->text('after_photo_url')->nullable();
            $table->integer('before_kilos');
            $table->integer('after_kilos')->nullable();
            $table->float('expenses')->nullable();
            $table->integer('fuel')->nullable();
            $table->integer('previous_fuel_amount');
            $table->integer('after_fuel_amount')->nullable();
            $table->text('destination');
            $table->date('returning_date')->nullable();
            $table->softDeletes();
                
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
