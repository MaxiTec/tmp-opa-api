<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audits_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('questions_id')->unsigned();
            $table->bigInteger('audits_id')->unsigned();

            $table->boolean('check')->default(0);
            $table->boolean('not_apply')->default(0);
            // Observaciones por pregunta, por si acaso
            $table->string('observations', 1000)->nullable();
            
            $table->timestamps();

            $table->index(['questions_id'], 'fk_audits_questions_to_questions');
            $table->foreign('questions_id', 'fk_audits_questions_to_questions')
                ->references('id')->on('questions')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->index(['audits_id'], 'fk_audits_questions_to_audits');
            $table->foreign('audits_id', 'fk_audits_questions_to_audits')
                ->references('id')->on('audits')
                ->onDelete('cascade')
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
        Schema::dropIfExists('audits_questions');
    }
}
