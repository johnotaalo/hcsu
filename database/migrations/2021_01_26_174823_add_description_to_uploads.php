<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionToUploads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('user_application_files', function (Blueprint $table) {
            $table->string('FILE_DESCRIPTION')->nullable()->after('FILE_URL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('user_application_files', function (Blueprint $table) {
            $table->dropColumn('FILE_DESCRIPTION');
        });
    }
}
