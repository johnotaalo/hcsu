<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeyToPrincipalArrivalDepartures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PRINCIPAL_ARRIVAL_DEPARTURES', function (Blueprint $table) {
            $table->index('HOST_COUNTRY_ID');
        });

        DB::unprepared('ALTER TABLE `PRINCIPAL_ARRIVAL_DEPARTURES` ADD CONSTRAINT `principal_arrival_principal_id_foreign` FOREIGN KEY (`HOST_COUNTRY_ID`) REFERENCES `pm_master_data`.`PRINCIPAL` (`HOST_COUNTRY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE `pm_master_data`.`PRINCIPAL_ARRIVAL_DEPARTURES` DROP FOREIGN KEY `principal_arrival_principal_id_foreign`;');

        Schema::table('PRINCIPAL_ARRIVAL_DEPARTURES', function (Blueprint $table) {
            $table->dropIndex(['HOST_COUNTRY_ID']);
        });
    }
}
