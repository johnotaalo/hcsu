<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMofaComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('DF_02', function (Blueprint $table) {
            $table->boolean('SUBMIT_TO_MOFA')->after('MANAGER_APPROVAL_COMMENTS')->nullable();
            $table->longText('SUBMIT_TO_MOFA_COMMENTS')->after('SUBMIT_TO_MOFA')->nullable();
            $table->boolean('MOFA_STATUS')->after('SUBMIT_TO_MOFA_COMMENTS')->nullable();
            $table->longText('MOFA_STATUS_COMMENTS')->after('MOFA_STATUS')->nullable();
            $table->string('IPMIS_NO')->after('MOFA_STATUS_COMMENTS')->nullable();
            $table->string('IPMIS_STATUS')->after('IPMIS_NO')->nullable();
            $table->longText('IPMIS_STATUS_COMMENT')->after('IPMIS_STATUS')->nullable();
            $table->longText('IPMIS_STATUS_REASON')->after('IPMIS_STATUS_COMMENT')->nullable();
            $table->boolean('DOCUMENT_COLLECTED')->after('IPMIS_STATUS_REASON')->nullable();
            $table->string('DOCUMENT_COLLECTED_BY')->after('DOCUMENT_COLLECTED')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('DF_02', function (Blueprint $table) {
            $table->dropColumns(['SUBMIT_TO_MOFA', 'SUBMIT_TO_MOFA_COMMENTS', 'MOFA_STATUS', 'MOFA_STATUS_COMMENTS', 'IPMIS_NO', 'IPMIS_STATUS', 'IPMIS_STATUS_COMMENT', 'IPMIS_STATUS_REASON', 'DOCUMENT_COLLECTED', 'DOCUMENT_COLLECTED_BY']);
        });
    }
}
