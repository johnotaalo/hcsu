<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubmitToMofaColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_01_ENDORSEMENT', function (Blueprint $table) {
            $table->string('SUBMIT_TO_MOFA')->after('MANAGER_COMMENT')->nullable();
            $table->string('SUBMIT_TO_MOFA_COMMENT')->after('SUBMIT_TO_MOFA')->nullable();
            $table->date('SUBMIT_TO_MOFA_DATE')->after('SUBMIT_TO_MOFA_COMMENT')->nullable();
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
            $table->dropColumn(['SUBMIT_TO_MOFA', 'SUBMIT_TO_MOFA_COMMENT', 'SUBMIT_TO_MOFA_DATE']);
        });
    }
}
