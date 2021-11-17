<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_programs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('programs_id')->unsigned();
            $table->bigInteger('audits_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();

            $table->boolean('is_active')->default(1);
            $table->boolean('is_visible')->default(1);
            $table->date('expiry_date');
            $table->date('executed_date');
            $table->date('deleted_date');
            // Una pregunta:  el nombre del auditor se pondra como un string?
            $table->string('auditor_name',200);
            // Para que sirve el campo de observaciones aca?
            $table->string('observations', 1000);

            $table->timestamps();

            $table->index(['programs_id'], 'fk_audit_programs_to_programs');
            $table->foreign('programs_id', 'fk_audit_programs_to_programs')
                ->references('id')->on('programs')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->index(['audits_id'], 'fk_audit_programs_to_audits');
            $table->foreign('audits_id', 'fk_audit_programs_to_audits')
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
        Schema::dropIfExists('audit_programs');
    }
}
