<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('manager', 100);
            $table->string('code', 45);
            $table->string('brand_img', 200)->nullable();
            $table->string('address', 250);
            $table->string('phone', 10);
            $table->string('phone_code', 2)->comment('only two digits for Mexican Numbers');
            $table->string('lat', 50)->nullable();
            $table->string('lon', 50)->nullable();
            $table->integer('rooms');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            // The name should'nt repeat
            $table->unique(['code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
