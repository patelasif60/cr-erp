<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoPersonPickupRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('roles_permissions')->insert(array(
            array(
                'module_title' => 'Non Person Pickup', 
                'module_link' => 'non_person_pickup',
                'type' => 'wms', 
                "sorting_order" => 10, 
                'wms_user' => 1, 
                'wms_manager' => 1)
            ));
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
