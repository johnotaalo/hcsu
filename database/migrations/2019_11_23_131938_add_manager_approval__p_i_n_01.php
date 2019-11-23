<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerApprovalPIN01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('PIN_01', function (Blueprint $table) {
            $table->boolean('MANAGER_APPROVAL')->after('NV_SERIAL_NO')->nullable();
            $table->text('MANAGER_APPROVAL_COMMENT')->after('MANAGER_APPROVAL')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('PIN_01', function (Blueprint $table) {
            $table->dropColumn(['MANAGER_APPROVAL', 'MANAGER_APPROVAL_COMMENT']);
        });
    }
}
