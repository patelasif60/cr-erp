<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePutAwayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('put_away', function (Blueprint $table) {
            $table->id();
            $table->string('etin')->nullable();
            $table->string('location')->nullable();
            $table->string('quantity')->nullable();
            $table->integer('summary_id')->nullable();
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
        Schema::dropIfExists('put_away');
    }
}
