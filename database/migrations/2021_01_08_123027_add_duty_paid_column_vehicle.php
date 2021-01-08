<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDutyPaidColumnVehicle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('VSR_01', function (Blueprint $table) {
            $table->string('DUTY_PAID')->after('SERIAL_NO')->default('NO');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('VSR_01', function (Blueprint $table) {
            $table->dropColumn(['DUTY_PAID']);
        });
    }
}
