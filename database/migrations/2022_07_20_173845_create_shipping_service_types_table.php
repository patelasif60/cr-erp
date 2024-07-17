<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingServiceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_service_types', function (Blueprint $table) {
            $table->id();
            $table->string('service_name')->nullable();
            $table->string('api_code')->nullable();
            $table->string('service_type')->nullable();
            $table->tinyInteger('is_active')->nullable()->default(1);
            $table->timestamps();
        });
        DB::table('shipping_service_types')
         ->insert(array(
            array('service_name' => 'Next Day Air','api_code'=>01,'service_type'=>'ups'),
            array('service_name' => '2nd Day Air','api_code'=>02,'service_type'=>'ups'),
            array('service_name' => 'Ground','api_code'=>03,'service_type'=>'ups'),
            array('service_name' => 'Express','api_code'=>07,'service_type'=>'ups'),
            array('service_name' => 'UPS Standard','api_code'=>11,'service_type'=>'ups'),
            array('service_name' => '3 Day Select','api_code'=>12,'service_type'=>'ups'),
            array('service_name' => 'Next Day Air Saver','api_code'=>13,'service_type'=>'ups'),
            array('service_name' => 'UPS Next Day Air® Early','api_code'=>14,'service_type'=>'ups'),
            array('service_name' => 'UPS Worldwide Economy DDU','api_code'=>17,'service_type'=>'ups'),
            array('service_name' => 'Express Plus','api_code'=>54,'service_type'=>'ups'),
            array('service_name' => '2nd Day Air A.M.','api_code'=>59,'service_type'=>'ups'),
            array('service_name' => 'UPS Saver','api_code'=>65,'service_type'=>'ups'),
            array('service_name' => 'UPS Worldwide Economy','api_code'=>72,'service_type'=>'ups'),
            array('service_name' => 'UPS Express','api_code'=>74,'service_type'=>'ups'),
            array('service_name' => 'UPS Today Standard','api_code'=>82,'service_type'=>'ups'),
            array('service_name' => 'UPS Today Dedicated Courier','api_code'=>83,'service_type'=>'ups'),
            array('service_name' => 'Expedited','api_code'=>07,'service_type'=>'ups'),
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_service_types');
    }
}
