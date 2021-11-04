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
            $table->bigIncrements('id');
            $table->bigInteger('section_id')->unsigned();
            $table->string('name');
            // control fields
            $table->boolean('is_active')->default(1);
            $table->boolean('status')->default(1);
            $table->timestamps();
            // a section shouldn't have a same area name
            $table->unique(['name', 'section_id']);
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
