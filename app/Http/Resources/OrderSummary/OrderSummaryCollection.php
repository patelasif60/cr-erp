<?php

namespace App\Http\Resources\OrderSummary;

use App\Http\Resources\OrderSummary\OrderSummaryResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderSummaryCollection extends ResourceCollection
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
        return OrderSummaryResource::collection($this->collection);
    }
}