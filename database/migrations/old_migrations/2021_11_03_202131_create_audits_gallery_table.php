<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsGalleryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audits_gallery', function (Blueprint $table) {
            // $table->id();
            $table->bigIncrements('id');
            // $table->unsignedInteger('audit_id');
            $table->bigInteger('audit_id')->unsigned();
            $table->string('url_img');
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
        Schema::dropIfExists('audits_gallery');
    }
}
