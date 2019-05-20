<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgencyFocalPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('2019')->create('ref_agency_focal_points', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('AGENCY_HOST_COUNTRY_ID');
            $table->string('INDEX_NO');
            $table->string('LAST_NAME');
            $table->string('OTHER_NAMES');
            $table->string('EXTENSION')->nullable();
            $table->string('MOBILE_NO')->nullable();
            $table->string('EMAIL')->unique();
            $table->boolean('STATUS')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('2019')->dropIfExists('agency_focal_points');
    }
}
