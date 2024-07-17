<?php

namespace App\Http\Resources\ProductTemperature;

use App\Http\Resources\ProductTemperature\ProductTemperatureResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductTemperatureCollection extends ResourceCollection
{
    public static $wrap = '';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return ProductTemperatureResource::collection($this->collection);
    }
}