<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStaffRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('AC_00', function (Blueprint $table) {
            $table->dropColumn([
                'IPMIS_NO',
                'IPMIS_STATUS',
                'IPMIS_COMMENT',
                'STAFF_REGISTRATION_LINK'
            ]);

            $table->text('NV_SERIAL_NO')->after('HOST_COUNTRY_ID')->nullable();
            $table->string('APPLICATION_TYPE')->after('NV_SERIAL_NO')->nullable();
            $table->longText('COMMENTS')->after('APPLICATION_TYPE')->nullable();
            $table->boolean('MANAGER_APPROVAL')->after('COMMENTS')->nullable();
            $table->longText('MANAGER_APPROVAL_COMMENT')->after('MANAGER_APPROVAL')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('AC_00', function (Blueprint $table) {
            $table->dropColumn([
                'NV_SERIAL_NO',
                'APPLICATION_TYPE',
                'COMMENTS',
                'MANAGER_APPROVAL',
                'MANAGER_APPROVAL_COMMENT'
            ]);

            $table->integer('IPMIS_NO')->nullable();
            $table->integer('IPMIS_STATUS')->nullable();
            $table->text('IPMIS_COMMENT')->nullable();
            $table->text('STAFF_REGISTRATION_LINK')->nullable();
        });
    }
}
