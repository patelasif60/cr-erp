<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLotAndExpDateInBackstockPalletItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('backstock_pallet_items', function (Blueprint $table) {
            $table->string('lot')->nullable();
            $table->string('exp_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('backstock_pallet_items', function (Blueprint $table) {
            //
        });
    }
}
