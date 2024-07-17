<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionToNewUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles_permissions', function (Blueprint $table) {
            $table->integer('wms_manager')->nullable()->default(0);
            $table->integer('wms_user')->nullable()->default(0);
        });
        DB::update('update roles_permissions set wms_manager = `user`, wms_user = `user`');
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
