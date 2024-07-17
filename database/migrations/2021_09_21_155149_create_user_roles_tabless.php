<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRolesTableSS extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('role')->nullable();
            $table->timestamps();
        });

        // Insert some stuff
        DB::table('user_roles')->insert(
            [
                [
                'role' => 'Admin'
                ],
                [
                    'role' => 'Manager'
                ],
                [
                    'role' => 'User'
                ]
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_roles');
    }
}
