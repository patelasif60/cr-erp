<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickPackMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pick_pack_method', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->string('frozen_pick')->nullable();
            $table->string('frozen_pack')->nullable();
            $table->string('dry_pick')->nullable();
            $table->string('dry_pack')->nullable();
            $table->string('refrig_pick')->nullable();
            $table->string('refrig_pack')->nullable();
            $table->timestamps();
        });

        DB::table('pick_pack_method')
                ->insert([
                        [
                            'client_id' => 0,
                            'frozen_pick' => 'Pick Sheet',
                            'frozen_pack' => 'Scan',
                            'dry_pick' => 'Scan',
                            'dry_pack' => 'Scan',
                            'refrig_pick' => 'Pick Sheet',
                            'refrig_pack' => 'Scan'
                        ]
                    ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pick_pack_method');
    }
}
