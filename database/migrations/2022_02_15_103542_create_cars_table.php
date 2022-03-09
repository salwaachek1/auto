<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('model');
            $table->text('serial_number');
            $table->text('place');
            $table->bigInteger('carburant_id')->unsigned();
            $table->foreign('carburant_id')->references('id')->on('carburants')->onDelete('cascade');     
            $table->string('kilo'); 
            $table->tinyInteger('is_dispo');
            $table->tinyInteger('is_working');
            $table->text('photo_url');
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
        Schema::dropIfExists('cars');
    }
}
