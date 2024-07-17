<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValuesForFedex extends Migration
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

        DB::table('shipping_service_types')->insert([
            [
                'service_name' => '2 Day',
                'service_type' => 'fedex',
                'is_active' => 1
            ],
            [
                'service_name' => '2 Day AM',
                'service_type' => 'fedex',
                'is_active' => 1
            ],
            [
                'service_name' => 'Express Saver',
                'service_type' => 'fedex',
                'is_active' => 1
            ],
            [
                'service_name' => 'Overnight',
                'service_type' => 'fedex',
                'is_active' => 1
            ],
            [
                'service_name' => 'Overnight EH',
                'service_type' => 'fedex',
                'is_active' => 1
            ],
            [
                'service_name' => 'Ground',
                'service_type' => 'fedex',
                'is_active' => 1
            ],
            [
                'service_name' => 'Home Delivery',
                'service_type' => 'fedex',
                'is_active' => 1
            ],
            [
                'service_name' => 'Priority Overnight',
                'service_type' => 'fedex',
                'is_active' => 1
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
        Schema::table('shipping_service_types', function (Blueprint $table) {
            //
        });
    }
}
