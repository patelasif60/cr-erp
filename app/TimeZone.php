<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeZone extends Model
{
    protected $table = 'time_zones';
    protected $fillable = [
        'name'
    ];


    public function storeTimezones(){
        $url = 'http://worldtimeapi.org/api/timezone';

        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);

            $data = curl_exec($curl);

            curl_close($curl);
            $output_data = json_decode($data);

            foreach ($output_data as $data) {
                self::updateOrCreate([
                    'name' => $data,
                ]);
            }

            return true;

        } catch (\Throwable $th) {
            return false;
        }



    }
}
