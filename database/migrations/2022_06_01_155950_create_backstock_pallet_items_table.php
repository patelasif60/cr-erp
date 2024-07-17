<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackstockPalletItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('backstock_pallet_items', function (Blueprint $table) {
            $table->id();
            $table->integer('backstock_pallet_id')->nullable();
            $table->string('ETIN')->nullable();
            $table->double('quantity')->default(0);
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
        Schema::dropIfExists('backstock_pallet_items');
    }
}
