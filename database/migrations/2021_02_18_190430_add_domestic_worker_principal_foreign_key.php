<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDomesticWorkerPrincipalForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PRINCIPAL_DOMESTIC_WORKER', function (Blueprint $table) {
            $table->index('PRINCIPAL_ID');
        });

        DB::unprepared('ALTER TABLE `PRINCIPAL_DOMESTIC_WORKER` ADD CONSTRAINT `principal_domestic_worker_host_country_id_foreign` FOREIGN KEY (`PRINCIPAL_ID`) REFERENCES `pm_master_data`.`PRINCIPAL` (`HOST_COUNTRY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE `pm_master_data`.`PRINCIPAL_DOMESTIC_WORKER` DROP FOREIGN KEY `principal_domestic_worker_host_country_id_foreign`;');

        Schema::table('PRINCIPAL_DOMESTIC_WORKER', function (Blueprint $table) {
            $table->dropIndex(['PRINCIPAL_ID']);
        });
    }
}
