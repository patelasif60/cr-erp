<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMasterShelfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_shelf', function (Blueprint $table) {   
            $table->unsignedBigInteger('location_type_id')->nullable();
            $table->string('slot')->nullable()->after('shelf');
            $table->string('ETIN')->nullable();
            $table->string('max_qty')->nullable();
            $table->string('cur_qty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_shelf', function (Blueprint $table) {
        });
    }
}
