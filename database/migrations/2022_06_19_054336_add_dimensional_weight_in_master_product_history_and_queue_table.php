<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDimensionalWeightInMasterProductHistoryAndQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_product_history', function (Blueprint $table) {
            //$table->decimal('dimensional_weight',8,2)->nullable()->after('weight');
            $table->integer('week_worth_qty')->default(0);
            $table->integer('min_order_qty')->default(0);
        });
        Schema::table('master_product_queue', function (Blueprint $table) {
            $table->decimal('dimensional_weight',8,2)->nullable()->after('weight');
            $table->integer('week_worth_qty')->default(0);
            $table->integer('min_order_qty')->default(0);
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_product_history_and_queue', function (Blueprint $table) {
            //
        });
    }
}
