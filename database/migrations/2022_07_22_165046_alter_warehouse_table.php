<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\WareHouse;
class AlterWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('warehouses', function (Blueprint $table) {   
            $table->string('address')->nullable()->after('warehouses');
            $table->string('address2')->nullable()->after('address');
            $table->string('country')->nullable()->after('address2');
            $table->string('state')->nullable()->after('country');
            $table->string('city')->nullable()->after('state');
            $table->string('zipcode')->nullable()->after('city');
            $table->string('phone_number')->nullable()->after('zipcode');
        });
        $data[1] = array(
                'address' => '2541 S Bayshore Dr.',
                'address2' => null,
                'country' => 'US',
                'state' => 'WI',
                'city' => 'Sister Bay',
                'zipcode' => 54234,
                'phone_number' => 8668586380,
            );
        $data[2] = array(
                'address' => '1143 Berry Hill St.',
                'address2' => null,
                'country' => 'US',
                'state' => 'PA',
                'city' => 'Harrisburg',
                'zipcode' => '17104',
                'phone_number' => 8668586380,
            );
        $data[3] = array(
                'address' => '1800 DEMING WAY',
                'address2' => null,
                'country' => 'US',
                'state' => 'NV',
                'city' => 'Sparks',
                'zipcode' => '89431',
                'phone_number' => 8668586380,
            );
        $data[4] = array(
                'address' => '6601 S AIR DEPOT BLVD',
                'address2' => null,
                'country' => 'US',
                'state' => 'OK',
                'city' => 'Oklahoma City',
                'zipcode' => '73135',
                'phone_number' => 8668586380,
            );
       foreach($data as $key => $val){
        $wareHouse = WareHouse::find($key);
        $wareHouse->address = $val['address'];
        $wareHouse->address2 = $val['address2'];
        $wareHouse->country = $val['country'];
        $wareHouse->state = $val['state'];
        $wareHouse->city = $val['city'];
        $wareHouse->zipcode = $val['zipcode'];
        $wareHouse->phone_number = $val['phone_number'];
        $wareHouse->save();
       }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('warehouses', function (Blueprint $table) {
        });
    }
}
