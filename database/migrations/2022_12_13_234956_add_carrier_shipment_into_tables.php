<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarrierShipmentIntoTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('shipping_service_types')->insert([
            [
                'service_name' => 'Non-person Pickup',
                'service_type' => 'non_person_pickup',
                'is_active' => 1
            ]
        ]);

        DB::table('carriers')->insert([
            [
                'company_name' => 'Non-person Pickup',
                'client_status' => 'Active'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
