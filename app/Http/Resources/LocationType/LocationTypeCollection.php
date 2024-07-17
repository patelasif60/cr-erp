<?php

namespace App\Http\Resources\LocationType;

use App\Http\Resources\LocationType\LocationTypeResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationTypeCollection extends ResourceCollection
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
        return LocationTypeResource::collection($this->collection);
    }
}