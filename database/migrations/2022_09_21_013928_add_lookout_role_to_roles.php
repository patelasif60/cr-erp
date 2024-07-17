<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLookoutRoleToRoles extends Migration
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
                    'module_title' => 'Product Lookout', 
                    'module_link' => 'product_lookout',
                    'type' => 'wms', 
                    "sorting_order" => 15,
                    'user' => 1,
                    'manager' => 1,
                    'administrator' => 1, 
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
        Schema::table('roles', function (Blueprint $table) {
            //
        });
    }
}
