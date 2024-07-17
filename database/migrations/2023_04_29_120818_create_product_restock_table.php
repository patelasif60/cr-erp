<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRestockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_restocks', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN')->nullable();
            $table->string('product_listing_name')->nullable();
            $table->string('client_supplier')->nullable();
            $table->string('upc')->nullable();
            $table->string('pick_location')->nullable();
            $table->string('backstock_location')->nullable();
            $table->string('pallet_id')->nullable();
            $table->string('qty_to_restock')->nullable();
            $table->string('priority')->nullable();
            $table->string('warehouse')->nullable();
            $table->string('client_supplier_id')->nullable();
            $table->string('supplier_type')->nullable();   
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
        Schema::dropIfExists('product_restock');
    }
}
