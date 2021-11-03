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
            // $table->id();
            $table->bigIncrements('id');
            // $table->unsignedInteger('user_id');
            $table->bigInteger('user_id')->unsigned();
            
            $table->string('observations');
            $table->boolean('is_active')->default(1);
            // Posibles estados para que el Gerente pueda revisar las auditorias
            $table->enum('status', ['pending', 'revised', 'unauthorized','authorized']);
            $table->timestamps();

            $table->index(['user_id'], 'fk_audits_to_users');
            $table->foreign('user_id', 'fk_audits_to_users')
                ->references('id')->on('users')
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
        Schema::dropIfExists('audits');
    }
}
