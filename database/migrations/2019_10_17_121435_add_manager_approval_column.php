<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerApprovalColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('VAT_01', function (Blueprint $table) {
            $table->string('MANAGER_REJECT_REASON')->after('MANAGER_APPROVAL')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('VAT_01', function (Blueprint $table) {
            $table->dropColumn('MANAGER_REJECT_REASON');
        });
    }
}
