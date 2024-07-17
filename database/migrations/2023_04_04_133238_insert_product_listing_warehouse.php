<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertProductListingWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('product_listing_filters')->insert([
            'label_name' => 'Warehouse',
            'column_name' => 'warehouse',
            'text_or_select'=>'Select',
            'select_table'=>'warehouses',
            'select_value_column'=>'warehouses',
            'select_label_column'=>'warehouses',
            'sorting_order'=>31,
            'is_default' => 1,
            'created_at' => '2023-04-04 22:57:43',
            'updated_at' => '2023-04-04 22:57:43',
            'type' => 'order'
        ]);
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
