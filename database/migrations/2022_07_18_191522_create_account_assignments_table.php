<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_order_account_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->integer('is_default')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('group_details')->nullable();
            $table->string('warehouse')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('rules')->nullable();
            $table->integer('dry_wi_carrier_id')->nullable();
            $table->integer('dry_wi_account_id')->nullable();
            $table->integer('dry_nv_carrier_id')->nullable();
            $table->integer('dry_nv_account_id')->nullable();
            $table->integer('dry_ok_carrier_id')->nullable();
            $table->integer('dry_ok_account_id')->nullable();
            $table->integer('dry_pa_carrier_id')->nullable();
            $table->integer('dry_pa_account_id')->nullable();

            $table->integer('frozen_wi_carrier_id')->nullable();
            $table->integer('frozen_wi_account_id')->nullable();
            $table->integer('frozen_nv_carrier_id')->nullable();
            $table->integer('frozen_nv_account_id')->nullable();
            $table->integer('frozen_ok_carrier_id')->nullable();
            $table->integer('frozen_ok_account_id')->nullable();
            $table->integer('frozen_pa_carrier_id')->nullable();
            $table->integer('frozen_pa_account_id')->nullable();

            $table->integer('refreg_wi_carrier_id')->nullable();
            $table->integer('refreg_wi_account_id')->nullable();
            $table->integer('refreg_nv_carrier_id')->nullable();
            $table->integer('refreg_nv_account_id')->nullable();
            $table->integer('refreg_ok_carrier_id')->nullable();
            $table->integer('refreg_ok_account_id')->nullable();
            $table->integer('refreg_pa_carrier_id')->nullable();
            $table->integer('refreg_pa_account_id')->nullable();
            $table->timestamps();
        });

        DB::table('carrier_order_account_assignments')->insert([
            array(
                'description' => 'Default',
                'is_default' => 1
            )
        ]);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_assignments');
    }
}
