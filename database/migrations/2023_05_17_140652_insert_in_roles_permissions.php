<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertInRolesPermissions extends Migration
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
                    'module_title' => 'Restock product Setting', 
                    'module_link' => 'restockproductsetting',
                    'type' => 'menus', 
                    "sorting_order" => 21,
                    'administrator' => 1
                    )
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
