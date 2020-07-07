<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePro1CSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('DF_03', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CASE_NO');
            $table->integer('HOST_COUNTRY_ID');
            $table->string('NV_SERIAL_NO');
            $table->integer('VEHICLE_ID');
            $table->string('VEHICLE_DATA_ORIGIN');
            $table->integer('DUTY_PAID');
            $table->string('NUMBER_PLATES_STATUS');
            $table->date('TRANSFER_DATE');
            $table->string('REASON_FOR_DISPOSAL');
            $table->string('BUYER_IDENTIFIED');
            $table->string('TYPE_OF_BUYER')->nullable();
            $table->boolean('HOST_COUNTRY_CLIENT')->nullable();
            $table->integer('BUYER_HOST_COUNTRY_ID')->nullable();
            $table->string('SALE_CURRENCY')->nullable();
            $table->string('SALE_PRICE')->nullable();
            $table->string('BUYER_FULL_NAMES')->nullable();
            $table->string('BUYER_AGENCY')->nullable();
            $table->string('BUYER_ADDRESS')->nullable();
            $table->string('BUYER_TOWN')->nullable();
            $table->string('BUYER_OCCUPATION')->nullable();
            $table->string('BUYER_EMPLOYER')->nullable();
            $table->string('BUYER_ID_NO')->nullable();
            $table->string('BUYER_PIN_NO')->nullable();
            $table->string('BUYER_PHONE_NO')->nullable();
            $table->string('BUYER_VEHICLE_USE')->nullable();
            $table->boolean('MANAGER_APPROVAL')->nullable();
            $table->text('MANAGER_APPROVAL_COMMENTS')->nullable();
            $table->boolean('SUBMIT_TO_MOFA')->nullable();
            $table->text('SUBMIT_TO_MOFA_COMMENTS')->nullable();
            $table->boolean('MOFA_STATUS')->nullable();
            $table->text('MOFA_STATUS_COMMENTS')->nullable();
            $table->string('IPMIS_NO')->nullable();
            $table->boolean('IPMIS_STATUS')->nullable();
            $table->text('IPMIS_COMMENT')->nullable();
            $table->string('IPMIS_STATUS_REASON')->nullable();
            $table->boolean('DOCUMENT_COLLECTED')->nullable();
            $table->string('DOCUMENT_COLLECTED_BY')->nullable();
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
        Schema::connection('pm_data')->dropIfExists('DF_03');
    }
}
