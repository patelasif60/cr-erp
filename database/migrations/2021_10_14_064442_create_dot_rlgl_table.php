<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDotRlglTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dot_rlgl', function (Blueprint $table) {
            $table->id();
            $table->string('dot_cust_num', 100)->nullable();
            $table->string('dot_cust_shipto', 100)->nullable();
            $table->string('gtin', 100)->nullable();
            $table->string('cust_item_num', 100)->nullable();
            $table->string('item_num', 100)->nullable();
            $table->string('mfg_number', 100)->nullable();
            $table->string('unabbreviated_desc', 100)->nullable();
            $table->string('supplier_name', 100)->nullable();
            $table->string('temp', 100)->nullable();
            $table->string('illinois_inventory_status', 100)->nullable();
            $table->string('maryland_inventory_status', 100)->nullable();
            $table->string('modesto_inventory_status', 100)->nullable();
            $table->string('oklahoma_inventory_status', 100)->nullable();
            $table->string('burley_inventory_status', 100)->nullable();
            $table->string('arizona_inventory_status', 100)->nullable();
            $table->string('stock_eta_date', 100)->nullable();
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
        Schema::dropIfExists('dot_rlgl');
    }
}
