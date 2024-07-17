<?php

namespace App\Http\Resources\Restock;

use App\Http\Resources\Restock\RestockResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RestockCollection extends ResourceCollection
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
        return RestockResource::collection($this->collection);
    }
}