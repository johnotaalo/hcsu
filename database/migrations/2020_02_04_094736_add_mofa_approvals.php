<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMofaApprovals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_01', function (Blueprint $table) {
            $table->string('MOFA_APPROVAL')->after('SUBMIT_TO_MOFA_COMMENT')->nullable();
            $table->string('MOFA_APPROVAL_COMMENT')->after('MOFA_APPROVAL')->nullable();
            $table->string('RNUMBER')->after('MOFA_APPROVAL_COMMENT')->nullable();
            $table->date('ISSUE_DATE')->after('RNUMBER')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('IM_01', function (Blueprint $table) {
            $table->dropColumn(['MOFA_APPROVAL', 'MOFA_APPROVAL_COMMENT', 'RNUMBER', 'ISSUE_DATE']);
        });
    }
}
