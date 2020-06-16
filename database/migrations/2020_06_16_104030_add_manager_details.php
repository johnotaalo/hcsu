<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('VSR_03', function (Blueprint $table) {
            $table->integer('MANAGER_APPROVAL')->nullable();
            $table->longText('MANAGER_APPROVAL_COMMENT')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('VSR_03', function (Blueprint $table) {
            $table->dropColumn(['MANAGER_APPROVAL', 'MANAGER_APPROVAL_COMMENT']);
        });
    }
}