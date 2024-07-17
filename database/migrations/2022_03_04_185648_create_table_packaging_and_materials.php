<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePackagingAndMaterials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packaging_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('supplier_id')->nullable();
            // $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->string('ETIN')->nullable();
            $table->text('product_description')->nullable();
            $table->unsignedInteger('material_type_id')->nullable();
            // $table->foreign('material_type_id')->references('id')->on('material_type')->onDelete('cascade');
            $table->string('quantity_per_bundle')->nullable();
            $table->string('bundle_qty_per_truck_load')->nullable();
            $table->string('product_temperature')->nullable();
            $table->string('supplier_product_number')->nullable();
            $table->string('UPC')->nullable();
            $table->double('weight',8,2)->nullable();
            $table->double('external_length',8,2)->nullable();
            $table->double('external_width',8,2)->nullable();
            $table->double('external_height',8,2)->nullable();
            $table->double('internal_length',8,2)->nullable();
            $table->double('internal_width',8,2)->nullable();
            $table->double('internal_height',8,2)->nullable();
            $table->double('capacity_cubic',8,2)->nullable();
            $table->double('cost',8,2)->nullable();
            $table->double('acquisition_cost',8,2)->nullable();
            $table->double('new_cost',8,2)->nullable();
            $table->date('new_cost_date')->nullable();
            $table->string('warehouses_assigned')->nullable();
            $table->string('product_assigned')->nullable();
            $table->string('clients_assigned')->nullable();
            $table->double('bluck_price',8,2)->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('packaging_materials');
    }
}
