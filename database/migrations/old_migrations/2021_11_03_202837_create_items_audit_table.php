<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items_audit', function (Blueprint $table) {
            // $table->id();
            $table->bigIncrements('id');
            // $table->unsignedInteger('audit_id');
            // $table->unsignedInteger('item_property_id');
            $table->bigInteger('audit_id')->unsigned();
            $table->bigInteger('item_property_id')->unsigned();
            $table->boolean('score')->default(false);
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
        Schema::dropIfExists('items_audit');
    }
}
