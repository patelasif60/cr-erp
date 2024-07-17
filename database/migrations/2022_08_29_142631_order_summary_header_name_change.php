<?php

use App\ProductListingFilter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderSummaryHeaderNameChange extends Migration
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

        ProductListingFilter::where('id', 65)->update(['label_name' => 'e-tailer Order Number']);
        ProductListingFilter::where('id', 67)->update(['label_name' => 'Order Source']);
        ProductListingFilter::where('id', 68)->update(['label_name' => 'Destination']);
        ProductListingFilter::where('id', 69)->update(['label_name' => 'Channel Delivery Date']);
        ProductListingFilter::where('id', 71)->update(['label_name' => 'Order Status']);
        ProductListingFilter::where('id', 66)->update(['column_name' => 'client_name']);
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
