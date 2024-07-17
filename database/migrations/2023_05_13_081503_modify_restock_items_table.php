<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyRestockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restock_items', function (Blueprint $table) {
            $table->dropColumn('restock_id');
            $table->dropColumn('type');
            $table->string('lot')->nullable();
            $table->string('upc')->nullable();
            $table->string('product_listing_name')->nullable();
            $table->integer('tranfered')->default(0);
            $table->integer('user_id')->nullable();
            $table->integer('warehouse_id')->nullable();
            $table->integer('tem_ref_id')->nullable();
            $table->text('ref_resp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
