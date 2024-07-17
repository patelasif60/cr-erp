<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShipRule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('roles_permissions')->insert(
            array(
                    'module_title' => 'Ship', 
                    'module_link' => 'ship',
                    'type' => 'wms', 
                    "sorting_order" => 14, 
                    'wms_user' => 1, 
                    'wms_manager' => 1)
                );
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
