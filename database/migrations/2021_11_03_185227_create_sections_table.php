<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            // $table->id();
            $table->bigIncrements('id');
            $table->string('name');
            $table->boolean('is_active')->default(1);
            $table->boolean('status')->default(1);
            $table->string('description')->nullable();
            $table->timestamps();
            // The name should'nt repeat
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }
}
