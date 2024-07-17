<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToProductTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_tickets', function (Blueprint $table) {
            $table->integer('master_product_id')->nullable()->after('description');
            $table->integer('status')->nullable()->after('master_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_tickets', function (Blueprint $table) {
            //
        });
    }
}
