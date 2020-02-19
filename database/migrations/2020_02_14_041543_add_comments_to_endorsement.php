<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentsToEndorsement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_01_ENDORSEMENT', function (Blueprint $table) {
            $table->longText('COMMENTS')->after('DEPENDENTS')->nullable();
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
            $table->dropColumn(['COMMENTS']);
        });
    }
}
