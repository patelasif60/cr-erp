<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertProductListingData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('product_listing_filters')->insert([
            'label_name' => 'Transit days',
            'column_name' => 'transit_days',
            'text_or_select'=>'Text',
            'sorting_order'=>30,
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
