<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audits', function (Blueprint $table) {

            $table->bigIncrements('id');
            // Relacion con id del Programa
            $table->bigInteger('programs_id')->unsigned();
            // Relacion con el id de la Auditoria
            // $table->bigInteger('audits_id')->unsigned();
            // Relacion con el id del usuario
            $table->bigInteger('user_id')->unsigned(); //Auditor
            $table->bigInteger('admin_id')->unsigned();
            // Control de Auditoria Soft Delete
            $table->boolean('is_active')->default(1);
            $table->boolean('is_visible')->default(1);
            
            $table->date('expiry_date')->nullable();
            // fecha de realizacion de la Auditoria
            $table->date('executed_date')->nullable();
            // Fecha de eliminacion de la auditoria
            $table->date('deleted_date')->nullable();
            $table->string('observations', 1000)->nullable();
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
        Schema::dropIfExists('audits');
    }
}
