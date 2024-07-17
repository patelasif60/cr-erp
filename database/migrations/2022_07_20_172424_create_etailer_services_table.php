<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtailerServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etailer_services', function (Blueprint $table) {
            $table->id();
            $table->string('etailer_service_name')->nullable();
            $table->integer('ups_service_type_id')->nullable();
            $table->string('ups_service_code')->nullable();
            $table->integer('fedex_service_type_id')->nullable();
            $table->string('fedex_service_code')->nullable();
            $table->timestamps();
        });

         DB::table('etailer_services')
         ->insert(array(
                array('etailer_service_name' => 'Ground'),
                array('etailer_service_name' => 'Overnight-Standard (Deliver by End of Day)'),
                array('etailer_service_name' => 'Overnight-Quick (Deliver by 10:30 AM)'),
                array('etailer_service_name' => 'Overnight-Early (Deliver by 8:30 AM)'),
                array('etailer_service_name' => 'Carrier with USPS')
            ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etailer_services');
    }
}
