<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManualColumnsToBlanket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('VAT_02', function (Blueprint $table) {
            $table->string('SUBMIT_TO_MOFA')->after('IPMIS_APPROVAL_DOCUMENT')->nullable();
            $table->string('SUBMIT_TO_MOFA_COMMENTS')->after('SUBMIT_TO_MOFA')->nullable();
            $table->string('MOFA_STATUS')->after('SUBMIT_TO_MOFA_COMMENTS')->nullable();
            $table->string('MOFA_COMMENTS')->after('MOFA_STATUS')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('VAT_02', function (Blueprint $table) {
            $table->dropColumn(['SUBMIT_TO_MOFA', 'SUBMIT_TO_MOFA_COMMENTS', 'MOFA_STATUS', 'MOFA_COMMENTS']);
        });
    }
}
