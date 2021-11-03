<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            // $table->id();
            $table->bigIncrements('id');
            // $table->unsignedBigInteger('area_id');
            $table->bigInteger('area_id')->unsigned();
            $table->string('name');
            $table->boolean('is_active')->default(1);
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->index(['area_id'], 'fk_items_to_areas');
            $table->foreign('area_id', 'fk_items_to_areas')
                ->references('id')->on('areas')
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
        Schema::dropIfExists('items');
    }
}
