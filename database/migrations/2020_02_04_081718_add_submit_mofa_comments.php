<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubmitMofaComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_01', function (Blueprint $table) {
            $table->string('SUBMIT_TO_MOFA')->after('IPMIS_COMMENT')->nullable();
            $table->text('SUBMIT_TO_MOFA_COMMENT')->after('SUBMIT_TO_MOFA')->nullable();
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
            $table->dropColumn(['SUBMIT_TO_MOFA', 'SUBMIT_TO_MOFA_COMMENT']);
        });
    }
}
