<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('main_point_of_contact')->after('status')->nullable();
            $table->string('address2')->after('address')->nullable();
            $table->date('next_order_date')->after('e_team_purchase_manager')->nullable();
            $table->string('cuttoff_time')->after('order_deadlines')->nullable();
            $table->string('owner')->after('order_portal_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('main_point_of_contact');
            $table->dropColumn('address2');
            $table->dropColumn('next_order_date');
            $table->dropColumn('cuttoff_time');
            $table->dropColumn('owner');
        });
    }
}
