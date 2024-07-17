<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageType extends Model
{
    protected $table = 'image_type';

    protected $fillable = [
        'image_type',
    ];

    public function imagetypeList(){
        $result = ImageType::get()->toArray();
        return $result;
    }


}
