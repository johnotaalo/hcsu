<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserAttributesFocalPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('2019')->table('ref_agency_focal_points', function (Blueprint $table) {
            $table->string('USERNAME')->unique();
            $table->string('PASSWORD')->nullable();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('2019')->table('ref_agency_focal_points', function (Blueprint $table) {
            $table->dropColumn(['USERNAME', 'PASSWORD']);
            $table->dropRememberToken();
        });
    }
}
