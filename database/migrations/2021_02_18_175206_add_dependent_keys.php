<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDependentKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("ALTER TABLE DEPENDENT_PASSPORTS MODIFY COLUMN DEPENDENT_ID INT(11) NOT NULL DEFAULT 0;");

        Schema::table('DEPENDENT_PASSPORTS', function (Blueprint $table) {
            $table->index('DEPENDENT_ID');
        });

        DB::unprepared('ALTER TABLE `DEPENDENT_PASSPORTS` ADD CONSTRAINT `principal_dependent_passport_foreign` FOREIGN KEY (`DEPENDENT_ID`) REFERENCES `pm_master_data`.`PRINCIPAL_DEPENDENT` (`HOST_COUNTRY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;');

        Schema::table('DEPENDENT_DIPLOMATIC_CARDS', function (Blueprint $table) {
            $table->index('HOST_COUNTRY_ID');
        });

        DB::unprepared('ALTER TABLE `DEPENDENT_DIPLOMATIC_CARDS` ADD CONSTRAINT `dependent_diplomatic_cards_foreign` FOREIGN KEY (`HOST_COUNTRY_ID`) REFERENCES `pm_master_data`.`PRINCIPAL_DEPENDENT` (`HOST_COUNTRY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE `pm_master_data`.`DEPENDENT_PASSPORTS` DROP FOREIGN KEY `principal_dependent_passport_foreign`;');

        Schema::table('DEPENDENT_PASSPORTS', function (Blueprint $table) {
            $table->dropIndex(['DEPENDENT_ID']);
        });

        DB::unprepared("ALTER TABLE DEPENDENT_PASSPORTS MODIFY COLUMN DEPENDENT_ID VARCHAR(191);");

        DB::unprepared('ALTER TABLE `pm_master_data`.`DEPENDENT_DIPLOMATIC_CARDS` DROP FOREIGN KEY `dependent_diplomatic_cards_foreign`;');

        Schema::table('DEPENDENT_DIPLOMATIC_CARDS', function (Blueprint $table) {
            $table->dropIndex(['HOST_COUNTRY_ID']);
        });

    }
}
