<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuorantineLocationForWiWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('master_aisle')->insert([
            array(
                'aisle_name' => 'N-1-1',
                'warehouse_id' => 1
            )
        ]);
        $aisle_id = DB::getPdo()->lastInsertId();

        DB::table('master_bay')->insert([
            array(
                'aisle_id' => $aisle_id,
                'type' => 'Pallet Rack',
                'bay_number' => 1,
                'no_of_shelf' => 1
            )
        ]);

        $bay_id = DB::getPdo()->lastInsertId();

        DB::table('master_shelf')->insert([
            array(
                'aisle_id' => $aisle_id,
                'bay_id' => $bay_id,
                'address' => 'N-1-1:1:1',
                'shelf' => 1,
                'slot' => 1
            )
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
