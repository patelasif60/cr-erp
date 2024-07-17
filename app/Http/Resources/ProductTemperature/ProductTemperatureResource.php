<?php

namespace App\Http\Resources\ProductTemperature;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductTemperatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_temperature' =>  $this->product_temperature
        ];
    }
}
