<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProcessingGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processing_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name')->nullable();
            $table->string('group_details')->nullable();
            $table->timestamps();
        });

        DB::table('processing_groups')
                ->insert(array(
                        array('group_name' => 'Dry'),
                        array('group_name' => 'Frozen'),
                        array('group_name' => 'Refrigerated')
                    ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('processing_groups');
    }
}
