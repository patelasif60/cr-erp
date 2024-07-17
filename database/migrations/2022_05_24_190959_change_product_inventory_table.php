<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProductInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_inventory', function (Blueprint $table) {
            $table->dropColumn('WI');
            $table->dropColumn('PA');
            $table->dropColumn('NV');
            $table->dropColumn('OKC');
            $table->unsignedInteger('warehouse_id')->nullable()->after('ETIN');
            $table->unsignedInteger('inventory')->nullable()->after('warehouse_id');
            $table->string('each_qty')->nullable()->after('inventory');

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
