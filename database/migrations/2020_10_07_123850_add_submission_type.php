<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubmissionType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('VAT_02', function (Blueprint $table) {
            $table->string('SUBMISSION_TYPE')->default('online')->after('HOST_COUNTRY_ID');
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
            $table->dropColumn(['SUBMISSION_TYPE']);
        });
    }
}
