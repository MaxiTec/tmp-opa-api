<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            // $table->id();
            $table->bigIncrements('id');
            // Relacion con las preguntas del hotel
            $table->bigInteger('area_criteria_id')->unsigned();
            // $table->bigInteger('criteria_id')->unsigned();
            // $table->boolean('check')->default(0);
            // $table->boolean('not_apply')->default(0);
            // // Observaciones por pregunta, por si acaso
            // $table->string('observations', 1000)->nullable();
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
        Schema::dropIfExists('questions');
    }
}
