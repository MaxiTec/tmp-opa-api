<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemPropertyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_property', function (Blueprint $table) {
            $table->id();
            // $table->unsignedInteger('property_id');
            $table->bigInteger('property_id')->unsigned();
            // $table->unsignedInteger('item_id');
            $table->bigInteger('item_id')->unsigned();
            $table->timestamps();


            $table->index(['property_id'], 'fk_item_property_to_properties');
            $table->foreign('property_id', 'fk_item_property_to_properties')
                ->references('id')->on('properties')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->index(['item_id'], 'fk_item_property_to_items');
            $table->foreign('item_id', 'fk_item_property_to_items')
                ->references('id')->on('items')
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
        Schema::dropIfExists('item_property');
    }
}
