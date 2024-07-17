<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sa_inventory', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 100)->nullable();
            $table->string('lob', 100)->nullable();
            $table->string('dot_id', 100)->nullable();
            $table->string('warehouse_code', 100)->nullable();
            $table->string('on_hand_quantity', 100)->nullable();
            $table->string('inventory_data_from', 100)->nullable();
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
        Schema::dropIfExists('sa_inventory');
    }
}
