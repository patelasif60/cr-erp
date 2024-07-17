<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPackModuleToPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles_permissions', function (Blueprint $table) {
            //
        });
        DB::table('roles_permissions')->insert(
            array(
                    'module_title' => 'Pack Order', 
                    'module_link' => 'pack',
                    'type' => 'wms', 
                    "sorting_order" => 13, 
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
        Schema::table('roles_permissions', function (Blueprint $table) {
            //
        });
    }
}
