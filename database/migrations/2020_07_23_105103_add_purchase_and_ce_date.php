<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPurchaseAndCeDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('VEHICLE_OWNER', function (Blueprint $table) {
            $table->date('REGISTRATION_DATE');
            $table->date('CUSTOMS_ENTRY_DATE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('VEHICLE_OWNER', function (Blueprint $table) {
            $table->dropColumn(['REGISTRATION_DATE', 'CUSTOMS_ENTRY_DATE']);
        });
    }
}
