<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRolesPermissionForNotification extends Migration
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
                    'module_title' => 'Product Management', 
                    'module_link' => 'product_management',
                    'type' => 'notification', 
                    "sorting_order" => 0
            ),
            array(
                'module_title' => 'Order Management', 
                'module_link' => 'order_management',
                'type' => 'notification', 
                "sorting_order" => 1
            ),
            array(
                'module_title' => 'Inventory Management', 
                'module_link' => 'inventory_management',
                'type' => 'notification', 
                "sorting_order" => 2
            )
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
