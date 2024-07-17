<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierKeheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_kehe', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN', 100)->default(null)->nullable();
            $table->string('etailer_stock_status', 100)->default(null)->nullable();
            $table->string('list_status', 100)->default(null)->nullable();
            $table->string('acquisition_cost', 100)->default(null)->nullable();
            $table->string('cust_number', 100)->default(null)->nullable();
            $table->string('cust_name', 100)->default(null)->nullable();
            $table->string('item_number', 100)->default(null)->nullable();
            $table->string('UPC', 100)->default(null)->nullable();
            $table->string('BRAND', 100)->default(null)->nullable();
            $table->string('DESCRIPTION', 100)->default(null)->nullable();
            $table->string('SIZE', 100)->default(null)->nullable();
            $table->string('UOM', 100)->default(null)->nullable();
            $table->string('QUANTITY', 100)->default(null)->nullable();
            $table->string('MIN_QUANTITY', 100)->default(null)->nullable();
            $table->string('UNIT_OF_SALE', 100)->default(null)->nullable();
            $table->string('CASEPACK', 100)->default(null)->nullable();
            $table->string('DISC_TYPE', 100)->default(null)->nullable();
            $table->string('PROMO_START', 100)->default(null)->nullable();
            $table->string('PROMO_END', 100)->default(null)->nullable();
            $table->string('DISC_$_OFF', 100)->default(null)->nullable();
            $table->string('DISCOUNTPERCENT', 100)->default(null)->nullable();
            $table->string('REG_WHLSL', 100)->default(null)->nullable();
            $table->string('WHLSL_MINUS_VOL', 100)->default(null)->nullable();
            $table->string('WHLSL_MINUS_DISC', 100)->default(null)->nullable();
            $table->string('NET_AMOUNT', 100)->default(null)->nullable();
            $table->string('SRP', 100)->default(null)->nullable();
            $table->string('DOWNLOAD_DATE', 100)->default(null)->nullable();
            $table->string('CATEGORY', 100)->default(null)->nullable();
            $table->string('ITEMCLASS', 100)->default(null)->nullable();
            $table->string('UPC Code', 100)->default(null)->nullable();
            $table->string('CATALOG', 100)->default(null)->nullable();
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
        Schema::dropIfExists('supplier_kehe');
    }
}
