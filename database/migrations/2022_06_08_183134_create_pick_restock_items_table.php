<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickRestockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pick_restock_items', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN', 140)->nullable();
            $table->string('backstock_address', 140)->nullable();
            $table->string('qty', 140)->nullable();
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
        Schema::dropIfExists('pick_restock_items');
    }
}
