<?php

use App\LocationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertValuesToLocationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_type', function (Blueprint $table) {
            LocationType::query()->truncate();
            DB::table('location_type')
                ->insert(array(
                        array('id' => '1','type' => 'Pick'),
                        array('id' => '2','type' => 'Backstock (Onsite)'),
                        array('id' => '3','type' => 'Backstock Offsite (Local)'),
                        array('id' => '4','type' => 'Backstock Offsite (PO Required)'),
                        array('id' => '5','type' => 'Put Away'),
                        array('id' => '6','type' => 'Outbound'),
                        array('id' => '7','type' => 'Quarantine')
                    )); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('location_type', function (Blueprint $table) {
            //
        });
    }
}
