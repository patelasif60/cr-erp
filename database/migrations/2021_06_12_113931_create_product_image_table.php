<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN');
            $table->string('Image_URL1_Primary');
            $table->string('Image_URL1_Alt_Text');
            $table->string('Image_URL2_Front');
            $table->string('Image_URL2_Alt_Text');
            $table->string('Image_URL3_Back');
            $table->string('Image_URL3_Alt_Text');
            $table->string('Image_URL4_Left');
            $table->string('Image_URL4_Alt_Text');
            $table->string('Image_URL5_Right');
            $table->string('Image_URL5_Alt_Text');
            $table->string('Image_URL6_Top');
            $table->string('Image_URL6_Alt_Text');
            $table->string('Image_URL7_Bottom');
            $table->string('Image_URL7_Alt_Text');
            $table->string('Image_URL8');
            $table->string('Image_URL8_Alt_Text');
            $table->string('Image_URL9');
            $table->string('Image_URL9_Alt_Text');
            $table->string('Image_URL10');
            $table->string('Image_URL10_Alt_Text');
            $table->string('Nutritional_Image_URL1');
            $table->string('Nutritional_Image_URL1_Alt_Text');
            $table->string('Nutritional_Image_URL2');
            $table->string('Nutritional_Image_URL2_Alt_Text');
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
        Schema::dropIfExists('product_image');
    }
}
