<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippedOrderPermissionIntoRolesTable extends Migration
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
                    array('module_title' => 'Shipped Orders', 'module_link' => 'shipped_orders',
                            'type' => 'wms', "sorting_order" => 12, 'wms_user' => 1, 'wms_manager' => 1),
                    
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
