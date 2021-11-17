<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audits', function (Blueprint $table) {
            // $table->id();
            $table->bigIncrements('id');
            // Aca tengo una duda, se supone que cada pregunta puede repetirse en  diferentes secciones y Areas,
            // que pasa si quiero relacionar la pregunta con la seccion de una auditoria, como sabré a que seccion pertenece
            // no seria mejor relacionarlo con area_criteria_id?
            $table->bigInteger('area_criteria_id')->unsigned();
            // $table->bigInteger('criteria_id')->unsigned();
            $table->boolean('check')->default(1);
            $table->boolean('not_apply')->default(1);
            $table->string('observations', 1000)->nullable();
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
        Schema::dropIfExists('audits');
    }
}
