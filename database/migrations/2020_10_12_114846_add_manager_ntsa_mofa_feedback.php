<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerNtsaMofaFeedback extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('DL_01', function (Blueprint $table) {
            $table->string('MANAGER_APPROVAL')->nullable();
            $table->text('MANAGER_COMMENT')->nullable();
            $table->string('SUBMIT_TO_MOFA')->nullable();
            $table->text('SUBMIT_TO_MOFA_COMMENT')->nullable();
            $table->string('MOFA_STATUS')->nullable();
            $table->text('MOFA_COMMENT')->nullable();
            $table->string('SUBMIT_TO_NTSA')->nullable();
            $table->text('SUBMIT_TO_NTSA_COMMENT')->nullable();
            $table->string('NTSA_STATUS')->nullable();
            $table->text('NTSA_COMMENT')->nullable();
            $table->string('DL_NO')->nullable();
            $table->date('DL_EXPIRY_DATE')->nullable();
            $table->string('DOCUMENT_COLLECTED_BY')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('DL_01', function (Blueprint $table) {
            $table->dropColumn([
                'MANAGER_APPROVAL',
                'MANAGER_COMMENT',
                'SUBMIT_TO_MOFA',
                'SUBMIT_TO_MOFA_COMMENT',
                'MOFA_STATUS',
                'MOFA_COMMENT',
                'SUBMIT_TO_NTSA',
                'SUBMIT_TO_NTSA_COMMENT',
                'NTSA_STATUS',
                'NTSA_COMMENT',
                'DL_NO',
                'DL_EXPIRY_DATE',
                'DOCUMENT_COLLECTED_BY'
            ]);
        });
    }
}
