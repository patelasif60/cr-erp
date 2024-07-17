<?php

namespace App\Http\Resources\Bay;

use App\Http\Resources\Bay\BayResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BayCollection extends ResourceCollection
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
        return BayResource::collection($this->collection);
    }
}