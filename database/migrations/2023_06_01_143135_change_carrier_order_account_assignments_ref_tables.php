<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCarrierOrderAccountAssignmentsRefTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carrier_order_account_assignments', function (Blueprint $table) {
            $table->renameColumn('refreg_wi_carrier_id', 'refrigerated_wi_carrier_id');
            $table->renameColumn('refreg_wi_account_id', 'refrigerated_wi_account_id');
            $table->renameColumn('refreg_nv_carrier_id', 'refrigerated_nv_carrier_id');
            $table->renameColumn('refreg_nv_account_id', 'refrigerated_nv_account_id');
            $table->renameColumn('refreg_ok_carrier_id', 'refrigerated_ok_carrier_id');
            $table->renameColumn('refreg_ok_account_id', 'refrigerated_ok_account_id');
            $table->renameColumn('refreg_pa_carrier_id', 'refrigerated_pa_carrier_id');
            $table->renameColumn('refreg_pa_account_id', 'refrigerated_pa_account_id');

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
