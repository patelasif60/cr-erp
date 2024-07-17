<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterShelfLotAndExpiry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_shelf_lot_and_expiry', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse')->nullable();
            $table->string('ETIN')->nullable();
            $table->string('address')->nullable();
            $table->string('qty')->nullable();
            $table->date('exp_date')->nullable();
            $table->string('lot')->nullable();
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
        Schema::dropIfExists('master_shelf_lot_and_expiry');
    }
}
