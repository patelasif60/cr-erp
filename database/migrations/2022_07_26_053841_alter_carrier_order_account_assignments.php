<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCarrierOrderAccountAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('carrier_order_account_assignments', function (Blueprint $table) {
            $table->string('client_channel_configurations_ids')->nullable();
            $table->longText('zipcode')->change()->nullable();
        });
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carrier_order_account_assignments', function (Blueprint $table) {
        });
    }
}
