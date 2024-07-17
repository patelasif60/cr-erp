<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductImagesQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_images_queue', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN')->nullable();
            $table->string('Image_URL1_Primary')->nullable();
            $table->string('Image_URL1_Alt_Text')->nullable();
            $table->string('Image_URL2_Front')->nullable();
            $table->string('Image_URL2_Alt_Text')->nullable();
            $table->string('Image_URL3_Back')->nullable();
            $table->string('Image_URL3_Alt_Text')->nullable();
            $table->string('Image_URL4_Left')->nullable();
            $table->string('Image_URL4_Alt_Text')->nullable();
            $table->string('Image_URL5_Right')->nullable();
            $table->string('Image_URL5_Alt_Text')->nullable();
            $table->string('Image_URL6_Top')->nullable();
            $table->string('Image_URL6_Alt_Text')->nullable();
            $table->string('Image_URL7_Bottom')->nullable();
            $table->string('Image_URL7_Alt_Text')->nullable();
            $table->string('Image_URL8')->nullable();
            $table->string('Image_URL8_Alt_Text')->nullable();
            $table->string('Image_URL9')->nullable();
            $table->string('Image_URL9_Alt_Text')->nullable();
            $table->string('Image_URL10')->nullable();
            $table->string('Image_URL10_Alt_Text')->nullable();
            $table->string('Nutritional_Image_URL1')->nullable();
            $table->string('Nutritional_Image_URL1_Alt_Text')->nullable();
            $table->string('Nutritional_Image_URL2')->nullable();
            $table->string('Nutritional_Image_URL2_Alt_Text')->nullable();
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
        Schema::dropIfExists('product_images_queue');
    }
}
