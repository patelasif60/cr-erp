<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToRolesPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles_permissions', function (Blueprint $table) {
            $table->string('type',255)->nullable()->after('module_title');
            $table->integer('sorting_order')->nullable()->after('administrator');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles_permissions', function (Blueprint $table) {
            $table->string('type',255)->nullable()->after('module_title');
            $table->integer('sorting_order',9)->nullable()->after('administrator');
        });
    }
}
