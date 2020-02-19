<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMofaFeedbackColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_01_ENDORSEMENT', function (Blueprint $table) {
            $table->string('MOFA_STATUS')->after('SUBMIT_TO_MOFA_DATE')->nullable();
            $table->longText('MOFA_COMMENT')->after('MOFA_STATUS')->nullable();
            $table->string('MOFA_STATUS_REASON')->after('MOFA_COMMENT')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('IM_01_ENDORSEMENT', function (Blueprint $table) {
            $table->dropColumn(['MOFA_STATUS', 'MOFA_COMMENT', 'MOFA_STATUS_REASON']);
        });
    }
}
