<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIpmisApprovalComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('AC_01', function (Blueprint $table) {
            $table->boolean('IPMIS_STATUS')->after('MANAGER_APPROVAL_COMMENT')->nullable();
            $table->text('IPMIS_STATUS_COMMENT')->after('IPMIS_STATUS')->nullable();
            $table->string('IPMIS_STATUS_REASON')->after('IPMIS_STATUS_COMMENT')->nullable();
            $table->string('IPMIS_CASE_NO')->after('IPMIS_STATUS_REASON')->nullable();
            $table->string('APPROVAL_DOCUMENT')->after('IPMIS_CASE_NO')->nullable();
            $table->string('CARD_NO')->after('APPROVAL_DOCUMENT')->nullable();
            $table->date('ISSUE_DATE')->after('CARD_NO')->nullable();
            $table->date('EXPIRY_DATE')->after('ISSUE_DATE')->nullable();
            $table->boolean('MOFA_COLLECTED')->after('EXPIRY_DATE')->nullable();
            $table->string('MOFA_COLLECTED_BY')->after('MOFA_COLLECTED')->nullable();
            $table->boolean('CARD_COLLECTED')->after('MOFA_COLLECTED_BY')->nullable();
            $table->string('CARD_COLLECTED_BY')->after('CARD_COLLECTED')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('AC_01', function (Blueprint $table) {
            $table->dropColumn(['IPMIS_STATUS', 'IPMIS_STATUS_COMMENT', 'IPMIS_STATUS_REASON', 'IPMIS_CASE_NO', 'APPROVAL_DOCUMENT', 'CARD_NO', 'ISSUE_DATE', 'EXPIRY_DATE', 'MOFA_COLLECTED', 'MOFA_COLLECTED_BY', 'CARD_COLLECTED', 'CARD_COLLECTED_BY'
            ]);
        });
    }
}
