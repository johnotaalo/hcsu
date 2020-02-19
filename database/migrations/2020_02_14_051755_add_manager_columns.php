<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_01_ENDORSEMENT', function (Blueprint $table) {
            $table->string("MANAGER_APPROVAL")->after("COMMENTS")->nullable();
            $table->string("MANAGER_COMMENT")->after("MANAGER_APPROVAL")->nullable();
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
            $table->dropColumn(['MANAGER_APPROVAL', 'MANAGER_COMMENT']);
        });
    }
}
