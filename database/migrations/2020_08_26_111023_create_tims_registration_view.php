<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimsRegistrationView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    protected $view = "VW_TIMS_REGISTRATIONS";
    public function up()
    {
        \DB::statement($this->dropView());
        \DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement($this->dropView());
    }

    private function dropView() : string {
        return <<<SQL
        DROP VIEW IF EXISTS `VW_TIMS_REGISTRATIONS`
        SQL;
    }

    private function createView() : string {
        return <<<SQL
        CREATE VIEW `VW_TIMS_REGISTRATIONS` AS SELECT
            TR.HOST_COUNTRY_ID,
            TR.DIP_ID_NO,
            TR.MOBILE_NO,
            TR.USERNAME,
            TR.KRA_PIN_NO,
            TR.REGISTRATION_DATE,
            (CASE 
                WHEN P.HOST_COUNTRY_ID IS NOT NULL THEN CONCAT(P.LAST_NAME,", ",P.OTHER_NAMES)
                WHEN D.HOST_COUNTRY_ID IS NOT NULL THEN CONCAT(D.LAST_NAME,", ",D.OTHER_NAMES)
            END) AS CLIENT,
            CONTRACT.ACRONYM
        FROM
            TIMS_REGISTRATION TR
            LEFT JOIN PRINCIPAL P ON P.HOST_COUNTRY_ID = TR.HOST_COUNTRY_ID
            LEFT JOIN PRINCIPAL_DEPENDENT D ON D.HOST_COUNTRY_ID = TR.HOST_COUNTRY_ID
            LEFT JOIN pm_data.VW_STAFF_LATEST_CONTRACT CONTRACT ON CONTRACT.PRINCIPAL_ID = P.HOST_COUNTRY_ID OR CONTRACT.PRINCIPAL_ID = D.PRINCIPAL_ID;
        SQL;
    }
}
