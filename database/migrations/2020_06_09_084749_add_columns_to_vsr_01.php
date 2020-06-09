<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToVsr01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('VSR_01', function (Blueprint $table) {
            $table->string('MANAGER_APPROVAL')->nullable();
            $table->text('MANAGER_APPROVAL_COMMENT')->nullable();
            $table->string('SUBMIT_TO_MOFA')->nullable();
            $table->text('SUBMIT_TO_MOFA_COMMENT')->nullable();
            $table->string('MOFA_APPROVAL')->nullable();
            $table->text('MOFA_APPROVAL_COMMENT')->nullable();
            $table->integer('LOGBOOK_CASE_NO')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('VSR_01', function (Blueprint $table) {
            $table->dropColumns(['MANAGER_APPROVAL', 'MANAGER_APPROVAL_COMMENT', 'SUBMIT_TO_MOFA', 'SUBMIT_TO_MOFA_COMMENT', 'MOFA_APPROVAL', 'MOFA_APPROVAL_COMMENT', 'LOGBOOK_CASE_NO']);
        });
    }
}
