<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('action')->nullable();
            $table->integer('task')->nullable();
            $table->string('details')->nullable();
            $table->string('etailer_order_number', 100)->nullable();
            $table->string('channel_order_number')->nullable();
            $table->string('client_order_number')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('type')->nullable();
            $table->date('order_date')->nullable();
            $table->dateTime('order_time')->nullable();
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
        Schema::dropIfExists('user_logs');
    }
}
