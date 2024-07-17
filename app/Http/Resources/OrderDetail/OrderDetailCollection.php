<?php

namespace App\Http\Resources\OrderDetail;

use App\Http\Resources\OrderDetail\OrderDetailResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderDetailCollection extends ResourceCollection
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
        return OrderDetailResource::collection($this->collection);
    }
}