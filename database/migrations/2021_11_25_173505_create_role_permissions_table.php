<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id')->nullable();
            $table->string('module_slug',255)->nullable();
            $table->integer('module_id')->nullable();
            $table->integer('access')->nullable();
            $table->integer('add')->nullable();
            $table->integer('edit')->nullable();
            $table->integer('view')->nullable();
            $table->integer('delete')->nullable();
            $table->integer('print')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_permissions');
    }
}
