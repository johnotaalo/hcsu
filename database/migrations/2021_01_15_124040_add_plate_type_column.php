<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlateTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('VSR_01', function (Blueprint $table) {
            $table->string('PLATE_TYPE')->default('diplomatic')->after('DUTY_PAID');
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
            $table->dropColumn(['PLATE_TYPE']);
        });
    }
}
