<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KitDescription extends Model
{
    protected $table = 'kit_description';

    protected $fillable = [
        'kit_description',
    ];

    public function KitDescriptionList(){
        $result = KitDescription::get()->toArray();
        return $result;
    }


}
