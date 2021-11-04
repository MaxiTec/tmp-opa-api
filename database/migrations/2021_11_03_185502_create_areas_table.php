<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            // $table->id();
            $table->bigIncrements('id');
            // $table->unsignedInteger('section_id');
            $table->bigInteger('section_id')->unsigned();
            $table->string('name');
            $table->boolean('is_active')->default(1);
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->index('section_id', 'fk_areas_to_sections');

            $table->foreign('section_id', 'fk_areas_to_sections')
                ->references('id')->on('sections')
                // /preguntar si se restringe o es en cascada (son catalogos)
                ->onDelete('restrict')
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
        Schema::dropIfExists('areas');
    }
}
