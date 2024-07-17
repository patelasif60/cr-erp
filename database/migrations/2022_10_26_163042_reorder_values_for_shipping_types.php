<?php

use App\ShippingServiceType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReorderValuesForShippingTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipping_service_types', function (Blueprint $table) {
            //
        });

        DB::delete('truncate table shipping_service_types');

        DB::table('shipping_service_types')->insert([
            [
                'service_name' => 'Ground',
                'service_type' => 'ups',
                'api_code' => '03',
                'is_active' => 1
            ],
            [
                'service_name' => 'Next Day Air Saver',
                'service_type' => 'ups',
                'api_code' => '13',
                'is_active' => 1
            ],
            [
                'service_name' => 'Next Day Air',
                'service_type' => 'ups',
                'api_code' => '01',
                'is_active' => 1
            ],
            [
                'service_name' => 'UPS Next Day Air® Early',
                'service_type' => 'ups',
                'api_code' => '14',
                'is_active' => 1
            ],
            [
                'service_name' => '2nd Day Air',
                'service_type' => 'ups',
                'api_code' => '02',
                'is_active' => 1
            ],
            [
                'service_name' => 'Express',
                'service_type' => 'ups',
                'api_code' => '07',
                'is_active' => 1
            ],
            [
                'service_name' => 'UPS Standard',
                'service_type' => 'ups',
                'api_code' => '11',
                'is_active' => 1
            ],
            [
                'service_name' => '3 Day Select',
                'service_type' => 'ups',
                'api_code' => '11',
                'is_active' => 1
            ],
            [
                'service_name' => 'UPS Worldwide Economy DDU',
                'service_type' => 'ups',
                'api_code' => '17',
                'is_active' => 1
            ],
            [
                'service_name' => 'Express Plus',
                'service_type' => 'ups',
                'api_code' => '54',
                'is_active' => 1
            ],
            [
                'service_name' => '2nd Day Air A.M.',
                'service_type' => 'ups',
                'api_code' => '59',
                'is_active' => 1
            ],
            [
                'service_name' => 'UPS Saver',
                'service_type' => 'ups',
                'api_code' => '65',
                'is_active' => 1
            ],
            [
                'service_name' => 'UPS Worldwide Economy',
                'service_type' => 'ups',
                'api_code' => '72',
                'is_active' => 1
            ],
            [
                'service_name' => 'UPS Express',
                'service_type' => 'ups',
                'api_code' => '74',
                'is_active' => 1
            ],
            [
                'service_name' => 'UPS Today Standard',
                'service_type' => 'ups',
                'api_code' => '82',
                'is_active' => 1
            ],
            [
                'service_name' => 'UPS Today Dedicated Courier',
                'service_type' => 'ups',
                'api_code' => '83',
                'is_active' => 1
            ],
            [
                'service_name' => 'Expedited',
                'service_type' => 'ups',
                'api_code' => '07',
                'is_active' => 1
            ],
            [
                'service_name' => 'Home Delivery',
                'service_type' => 'fedex',
                'api_code' => null,
                'is_active' => 1
            ],
            [
                'service_name' => 'Ground',
                'service_type' => 'fedex',
                'api_code' => null,
                'is_active' => 1
            ],
            [
                'service_name' => 'Overnight',
                'service_type' => 'fedex',
                'api_code' => null,
                'is_active' => 1
            ],
            [
                'service_name' => 'Priority Overnight',
                'service_type' => 'fedex',
                'api_code' => null,
                'is_active' => 1
            ],
            [
                'service_name' => '2 Day',
                'service_type' => 'fedex',
                'api_code' => null,
                'is_active' => 1
            ],
            [
                'service_name' => '2 Day AM',
                'service_type' => 'fedex',
                'api_code' => null,
                'is_active' => 1
            ],            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipping_service_types', function (Blueprint $table) {
            //
        });
    }
}
