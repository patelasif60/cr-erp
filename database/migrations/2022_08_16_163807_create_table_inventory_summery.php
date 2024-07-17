<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInventorySummery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_summery', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN')->nullable();
            $table->string('parent_ETIN')->nullable();
            $table->double('wi_qty')->nullable();
            $table->double('wi_each_qty')->nullable();
            $table->double('wi_orderable_qty')->nullable();
            $table->double('wi_fulfilled_qty')->nullable();
            $table->double('wi_open_order_qty')->nullable();
            $table->double('pa_qty')->nullable();
            $table->double('pa_each_qty')->nullable();
            $table->double('pa_orderable_qty')->nullable();
            $table->double('pa_fulfilled_qty')->nullable();
            $table->double('pa_open_order_qty')->nullable();
            $table->double('nv_qty')->nullable();
            $table->double('nv_each_qty')->nullable();
            $table->double('nv_orderable_qty')->nullable();
            $table->double('nv_fulfilled_qty')->nullable();
            $table->double('nv_open_order_qty')->nullable();
            $table->double('okc_qty')->nullable();
            $table->double('okc_each_qty')->nullable();
            $table->double('okc_orderable_qty')->nullable();
            $table->double('okc_fulfilled_qty')->nullable();
            $table->double('okc_open_order_qty')->nullable();
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
        Schema::dropIfExists('table_inventory_summery');
    }
}
