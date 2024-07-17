<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_inventory', function (Blueprint $table) {
            $table->string('OKC',100)->default('0')->nullable()->after('W3_Orderable_Quantity');
        });

        Schema::table('product_inventory_queue', function (Blueprint $table) {
            $table->string('OKC',100)->default('0')->nullable()->after('W3_Orderable_Quantity');
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
