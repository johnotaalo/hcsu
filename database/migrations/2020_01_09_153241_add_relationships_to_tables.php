<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationshipsToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        // Schema::table('PRINCIPAL', function (Blueprint $table) {
        //     $table->primary('HOST_COUNTRY_ID');
        // });
        DB::unprepared('ALTER TABLE `PRINCIPAL` DROP PRIMARY KEY, ADD PRIMARY KEY (  `ID` ,  `HOST_COUNTRY_ID` )');
        DB::unprepared('ALTER TABLE `PRINCIPAL_DEPENDENT` DROP PRIMARY KEY, ADD PRIMARY KEY (  `ID` ,  `HOST_COUNTRY_ID` )');

        DB::unprepared('UPDATE PRINCIPAL_DEPENDENT SET DATE_OF_BIRTH = "1970-01-01" where DATE_OF_BIRTH = "0000-00-00"');
        DB::unprepared('UPDATE PRINCIPAL_DIPLOMATIC_CARDS SET EXPIRY_DATE = "1970-01-01" where EXPIRY_DATE = "0000-00-00"');
        DB::unprepared('UPDATE PRINCIPAL_DIPLOMATIC_CARDS SET ISSUE_DATE = "1970-01-01" where ISSUE_DATE = "0000-00-00"');
        DB::unprepared('UPDATE DIPLOMATIC_ID SET EXPIRY_DATE = "1970-01-01" where EXPIRY_DATE = "0000-00-00"');
        DB::unprepared('UPDATE DIPLOMATIC_ID SET ISSUE_DATE = "1970-01-01" where ISSUE_DATE = "0000-00-00"');
        DB::unprepared('DELETE FROM PRINCIPAL_DEPENDENT WHERE PRINCIPAL_ID IS NULL OR PRINCIPAL_ID NOT IN (SELECT HOST_COUNTRY_ID FROM PRINCIPAL)');
        DB::unprepared('DELETE FROM PRINCIPAL_PASSPORT WHERE PRINCIPAL_ID IS NULL OR PRINCIPAL_ID NOT IN (SELECT ID FROM PRINCIPAL)');
        DB::unprepared('DELETE FROM PRINCIPAL_CONTRACT WHERE PRINCIPAL_ID IS NULL OR PRINCIPAL_ID NOT IN (SELECT HOST_COUNTRY_ID FROM PRINCIPAL)');
        DB::unprepared('DELETE FROM PRINCIPAL_DIPLOMATIC_CARDS WHERE HOST_COUNTRY_ID IS NULL OR HOST_COUNTRY_ID NOT IN (SELECT HOST_COUNTRY_ID FROM PRINCIPAL)');
        DB::unprepared('DELETE FROM DIPLOMATIC_ID WHERE HOST_COUNTRY_ID IS NULL OR HOST_COUNTRY_ID NOT IN (SELECT HOST_COUNTRY_ID FROM PRINCIPAL)');

        Schema::table('PRINCIPAL', function (Blueprint $table) {
            $table->index('HOST_COUNTRY_ID');
            $table->index('ID');
            $table->index('LAST_NAME');
            $table->index('OTHER_NAMES');
            $table->index('EMAIL');
            $table->index('PIN_NO');
        });

        Schema::table('PRINCIPAL_DEPENDENT', function (Blueprint $table) {
            $table->index('PRINCIPAL_ID');
        });

        Schema::table('PRINCIPAL_PASSPORT', function (Blueprint $table) {
            $table->index('PRINCIPAL_ID');
        });

        Schema::table('PRINCIPAL_CONTRACT', function (Blueprint $table) {
            $table->index('PRINCIPAL_ID');
        });

        Schema::table('PRINCIPAL_DIPLOMATIC_CARDS', function(Blueprint $table){
            $table->index('HOST_COUNTRY_ID');
        });

        Schema::table('DIPLOMATIC_ID', function(Blueprint $table){
            $table->index('HOST_COUNTRY_ID');
        });

        DB::unprepared('ALTER TABLE `PRINCIPAL_DEPENDENT` ADD CONSTRAINT `principal_dependent_principal_id_foreign` FOREIGN KEY (`PRINCIPAL_ID`) REFERENCES `pm_master_data`.`PRINCIPAL` (`HOST_COUNTRY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;');
        DB::unprepared('ALTER TABLE `PRINCIPAL_PASSPORT` ADD CONSTRAINT `principal_passport_principal_id_foreign` FOREIGN KEY (`PRINCIPAL_ID`) REFERENCES `PRINCIPAL` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;');
        DB::unprepared('ALTER TABLE `PRINCIPAL_CONTRACT` ADD CONSTRAINT `principal_contract_principal_id_foreign` FOREIGN KEY (`PRINCIPAL_ID`) REFERENCES `PRINCIPAL` (`HOST_COUNTRY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;');
        DB::unprepared('ALTER TABLE `PRINCIPAL_DIPLOMATIC_CARDS` ADD CONSTRAINT `principal_diplomatic_cards_host_country_id_foreign` FOREIGN KEY (`HOST_COUNTRY_ID`) REFERENCES `PRINCIPAL` (`HOST_COUNTRY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;');
        DB::unprepared('ALTER TABLE `DIPLOMATIC_ID` ADD CONSTRAINT `diplomatic_id_host_country_id_foreign` FOREIGN KEY (`HOST_COUNTRY_ID`) REFERENCES `PRINCIPAL` (`HOST_COUNTRY_ID`) ON DELETE CASCADE ON UPDATE CASCADE;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE `PRINCIPAL` DROP PRIMARY KEY, ADD PRIMARY KEY (  `ID` )');
        DB::unprepared('ALTER TABLE `PRINCIPAL_DEPENDENT` DROP PRIMARY KEY, ADD PRIMARY KEY (  `ID` )');

        DB::unprepared('ALTER TABLE `pm_master_data`.`PRINCIPAL_DEPENDENT` DROP FOREIGN KEY `principal_dependent_principal_id_foreign`;');
        DB::unprepared('ALTER TABLE `pm_master_data`.`PRINCIPAL_PASSPORT` DROP FOREIGN KEY `principal_passport_principal_id_foreign`;');
        DB::unprepared('ALTER TABLE `pm_master_data`.`PRINCIPAL_CONTRACT` DROP FOREIGN KEY `principal_contract_principal_id_foreign`;');
        DB::unprepared('ALTER TABLE `pm_master_data`.`PRINCIPAL_CONTRACT` DROP FOREIGN KEY `principal_diplomatic_cards_host_country_id_foreign`;');
        DB::unprepared('ALTER TABLE `pm_master_data`.`PRINCIPAL_CONTRACT` DROP FOREIGN KEY `diplomatic_id_host_country_id_foreign`;');

        Schema::table('PRINCIPAL', function (Blueprint $table) {
            $table->dropIndex(['HOST_COUNTRY_ID']);
            $table->dropIndex(['ID']);
            $table->dropIndex(['LAST_NAME']);
            $table->dropIndex(['OTHER_NAMES']);
            $table->dropIndex(['EMAIL']);
            $table->dropIndex(['PIN_NO']);
        });

        Schema::table('PRINCIPAL_DEPENDENT', function (Blueprint $table) {
            $table->dropIndex(['PRINCIPAL_ID']);
        });

        Schema::table('PRINCIPAL_PASSPORT', function (Blueprint $table) {
            $table->dropIndex(['PRINCIPAL_ID']);
        });

        Schema::table('PRINCIPAL_CONTRACT', function (Blueprint $table) {
            $table->dropIndex(['PRINCIPAL_ID']);
        });

        Schema::table('PRINCIPAL_CONTRACT', function (Blueprint $table) {
            $table->dropIndex(['HOST_COUNTRY_ID']);
        });

        Schema::table('PRINCIPAL_CONTRACT', function (Blueprint $table) {
            $table->dropIndex(['HOST_COUNTRY_ID']);
        });
    }
}
