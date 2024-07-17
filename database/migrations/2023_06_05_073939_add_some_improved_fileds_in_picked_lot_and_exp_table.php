<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeImprovedFiledsInPickedLotAndExpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('picked_lot_and_exp', function (Blueprint $table) {
            $table->double('unit_in_pack')->nullable();
            $table->string('Main_ETIN')->nullable();
            $table->integer('FromTheParent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('picked_lot_and_exp', function (Blueprint $table) {
            //
        });
    }
}
