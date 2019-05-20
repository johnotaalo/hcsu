<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTableNamesToCaps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename("diplomatic_id", "DIPLOMATIC_ID");
        Schema::rename("driving_license", "DRIVING_LICENSE");
        Schema::rename("principal", "PRINCIPAL");
        Schema::rename("principal_contract", "PRINCIPAL_CONTRACT");
        Schema::rename("principal_contract_renewals", "PRINCIPAL_CONTRACT_RENEWALS");
        Schema::rename("principal_dependent", "PRINCIPAL_DEPENDENT");
        Schema::rename("principal_passport", "PRINCIPAL_PASSPORT");
        Schema::rename("principal_passport_validity", "PRINCIPAL_PASSPORT_VALIDITY");
        Schema::rename("vehicle_owner", "VEHICLE_OWNER");
        Schema::rename("vehicle", "VEHICLE");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename("DIPLOMATIC_ID", "diplomatic_id");
        Schema::rename("DRIVING_LICENSE", "driving_license");
        Schema::rename("PRINCIPAL", "principal");
        Schema::rename("PRINCIPAL_CONTRACT", "principal_contract");
        Schema::rename("PRINCIPAL_CONTRACT_RENEWALS", "principal_contract_renewals");
        Schema::rename("PRINCIPAL_DEPENDENT", "principal_dependent");
        Schema::rename("PRINCIPAL_PASSPORT", "principal_passport");
        Schema::rename("PRINCIPAL_PASSPORT_VALIDITY", "principal_passport_validity");
        Schema::rename("VEHICLE_OWNER", "vehicle_owner");
        Schema::rename("VEHICLE", "vehicle");
    }
}
