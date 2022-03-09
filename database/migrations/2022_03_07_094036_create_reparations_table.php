<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReparationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reparations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('garage');
            $table->text('diagnosis')->nullable();
            $table->text('replaced_parts')->nullable();
            $table->bigInteger('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');     
            $table->Integer('fees')->nullable();
            $table->tinyInteger('is_done');
            $table->date('date_out')->nullable();
            $table->string('phone');
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
        Schema::dropIfExists('reparations');
    }
}
