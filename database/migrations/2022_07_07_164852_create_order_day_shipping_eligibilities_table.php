<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDayShippingEligibilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_day_shipping_eligibilities', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->string('temperature')->nullable();
            $table->string('day')->nullable();
            $table->timestamps();
            });

            DB::table('order_day_shipping_eligibilities')
                ->insert(array(
                        array('description' => 'FRZ | 4Day','temperature' => 'Frozen', 'day' => '4'),
                        array('description' => 'FRZ | 3Day','temperature' => 'Frozen', 'day' => '3'),
                        array('description' => 'FRZ | 2Day','temperature' => 'Frozen', 'day' => '2'),
                        array('description' => 'FRZ | 1Day','temperature' => 'Frozen', 'day' => '1'),
                        array('description' => 'REF | 4Day','temperature' => 'Refrigerated', 'day' => '4'),
                        array('description' => 'REF | 3Day','temperature' => 'Refrigerated', 'day' => '3'),
                        array('description' => 'REF | 2Day','temperature' => 'Refrigerated', 'day' => '2'),
                        array('description' => 'REF | 1Day','temperature' => 'Refrigerated', 'day' => '1'),
                        array('description' => 'DRY | 4Day','temperature' => 'DRY', 'day' => '4'),
                        array('description' => 'DRY | 3Day','temperature' => 'DRY', 'day' => '3'),
                        array('description' => 'DRY | 2Day','temperature' => 'DRY', 'day' => '2'),
                        array('description' => 'DRY | 1Day','temperature' => 'DRY', 'day' => '1')
                    ));

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_day_shipping_eligibilities');
    }
}
