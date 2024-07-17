<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModifyValuesInProductListingFilters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_listing_filters', function (Blueprint $table) {
            
        });
        DB::table('product_listing_filters')->where('id', '66')->update([
            'text_or_select' => 'Select',
            'select_table' => 'clients',
            'select_value_column' => 'id',
            'select_label_column' => 'company_name'
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
