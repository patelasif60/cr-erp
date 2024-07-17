<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report');
            $table->string('report_type');
            $table->string('client_id')->nullable();
            $table->string('warehouseId')->nullable();
            $table->string('from_date')->nullable();
            $table->string('to_date')->nullable();
            $table->string('own_report_field')->nullable();
            $table->boolean('status')->default(1);
            $table->enum('schedule_type', ['daily', 'weekly','monthly']);
            $table->string('schedule_value')->nullable();
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
        Schema::dropIfExists('schedule_reports');
    }
}
