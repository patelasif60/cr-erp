<?php

namespace App\Http\Resources\Aisle;

use App\Http\Resources\Aisle\AisleResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AisleCollection extends ResourceCollection
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
        return AisleResource::collection($this->collection);
    }
}