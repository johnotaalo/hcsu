<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerApprovalIm01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_01', function (Blueprint $table) {
            $table->boolean('MANAGER_APPROVAL')->after('JUSTIFICATION')->nullable();
            $table->text('MANAGER_COMMENT')->after('MANAGER_APPROVAL')->nullable();
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
            $table->dropColumn(['MANAGER_APPROVAL', 'MANAGER_COMMENT']);
        });
    }
}
