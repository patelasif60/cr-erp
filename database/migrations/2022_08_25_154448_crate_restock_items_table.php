<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrateRestockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restock_items', function (Blueprint $table) {
            $table->id();
            $table->integer('restock_id')->nullable();
            $table->string('ETIN')->nullable();
            $table->double('quantity')->nullable();
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
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
        //
    }
}
