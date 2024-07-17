<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaveStoreAutomatorExportStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('save_store_automator_export_statuses', function (Blueprint $table) {
            $table->id();
			$table->string('file_name')->nullable();
			$table->string('file_path')->nullable();
			$table->string('process_time')->nullable();
			$table->string('status')->nullable();
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
        Schema::dropIfExists('save_store_automator_export_statuses');
    }
}
