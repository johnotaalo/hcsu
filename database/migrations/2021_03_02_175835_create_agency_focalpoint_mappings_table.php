<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgencyFocalpointMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("2019")->create('agency_focalpoint_mappings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("FOCAL_POINT_ID");
            $table->integer("AGENCY_ID");
            $table->index(['FOCAL_POINT_ID', 'AGENCY_ID']);
            $table->foreign('FOCAL_POINT_ID')->references('ID')->on('ref_agency_focal_points');
            $table->foreign('AGENCY_ID')->references('AGENCY_ID')->on('agencies');
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
        Schema::connection("2019")->dropIfExists('agency_focalpoint_mappings');
    }
}
