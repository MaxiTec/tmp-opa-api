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
            $table->id();
            $table->string('name', 100);
            $table->string('general_manager', 100);
            $table->string('code', 45);
            $table->string('brand_img', 100);
            $table->string('address', 100);
            $table->string('phone', 50);
            $table->string('lat_map', 50);
            $table->string('long_map', 50);
            $table->string('no_rooms', 20);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->unique(['name','code'], 'uk_properties_name_code');
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
