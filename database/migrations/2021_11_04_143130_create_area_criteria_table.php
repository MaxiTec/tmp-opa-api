<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreaCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area_criteria', function (Blueprint $table) {
            // $table->id();
            $table->bigIncrements('id');
            $table->bigInteger('area_id')->unsigned();
            $table->bigInteger('criteria_id')->unsigned();

            $table->timestamps();

            $table->index(['area_id'], 'fk_area_criteria_to_area');
            $table->foreign('area_id', 'fk_area_criteria_to_area')
                ->references('id')->on('areas')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->index(['criteria_id'], 'fk_area_criteria_to_criteria');
            $table->foreign('criteria_id', 'fk_area_criteria_to_criteria')
                ->references('id')->on('criteria')
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
        Schema::dropIfExists('area_criteria');
    }
}
