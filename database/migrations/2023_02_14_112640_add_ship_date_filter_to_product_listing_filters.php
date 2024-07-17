<?php

use App\ProductListingFilter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShipDateFilterToProductListingFilters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_listing_filters', function (Blueprint $table) {
            //
        });
        ProductListingFilter::create([
            'label_name' => 'Ship Date',
            'column_name' => 'ship_date',
            'text_or_select' => 'Date',
            'sorting_order' => 29,
            'is_default' => 1,
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
        Schema::table('product_listing_filters', function (Blueprint $table) {
            //
        });
    }
}
