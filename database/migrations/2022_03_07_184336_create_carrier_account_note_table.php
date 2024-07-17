<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierAccountNoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_account_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->string('event')->nullable();
            $table->string('details')->nullable();
            $table->string('user')->nullable();
            $table->string('date_and_time')->nullable();
            $table->integer('added_by')->nullable();
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
        Schema::dropIfExists('carrier_account_notes');
    }
}
