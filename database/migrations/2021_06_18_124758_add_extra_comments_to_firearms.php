<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraCommentsToFirearms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('FP_01', function (Blueprint $table) {
            $table->longText('EXTRA_COMMENTS')->after('STAFF_MEMBER_DETAILS')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('FP_01', function (Blueprint $table) {
            $table->dropColumn(['EXTRA_COMMENTS']);
        });
    }
}
