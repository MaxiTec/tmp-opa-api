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
            $table->bigIncrements('id');
            $table->bigInteger('area_criteria_id')->unsigned();
            $table->bigInteger('property_id')->unsigned();
            $table->timestamps();
            // restriccion un hotel no puede repetir mismo criterio (?)
            $table->unique(['area_criteria_id','property_id']);

            $table->index(['area_criteria_id'], 'fk_audits_to_area_criteria');
            $table->foreign('area_criteria_id', 'fk_audits_to_area_criteria')
                ->references('id')->on('area_criteria')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->index(['property_id'], 'fk_audits_to_properties');
            $table->foreign('property_id', 'fk_audits_to_properties')
                ->references('id')->on('properties')
                ->onDelete('cascade')
                ->onUpdate('restrict');
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
