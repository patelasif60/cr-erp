<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackageNumberInOrderPickAndPackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_pick_and_pack', function (Blueprint $table) {
            $table->string('package_number')->nullable()->after('pack_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_pick_and_pack', function (Blueprint $table) {
            //
        });
    }
}
