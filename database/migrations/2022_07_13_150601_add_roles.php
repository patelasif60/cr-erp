<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles_permissions', function (Blueprint $table) {
            DB::table('roles_permissions')
                ->insert(array(
                    array('module_title' => 'Sub Orders', 'module_link' => 'open_orders',
                            'type' => 'wms', "sorting_order" => 10, 'wms_user' => 1, 'wms_manager' => 1),
                    array('module_title' => 'Picker Orders', 'module_link' => 'picker_orders',
                            'type' => 'wms', "sorting_order" => 11, 'wms_user' => 1, 'wms_manager' => 1),
                    
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
        //
    }
}
