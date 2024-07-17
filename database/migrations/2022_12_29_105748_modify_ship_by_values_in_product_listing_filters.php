<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyShipByValuesInProductListingFilters extends Migration
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
        DB::table('product_listing_filters')->where('id', 70)->update([
            'text_or_select' => 'Date'
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
