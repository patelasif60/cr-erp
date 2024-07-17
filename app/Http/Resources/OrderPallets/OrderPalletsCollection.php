<?php

namespace App\Http\Resources\OrderPallets;

use App\Http\Resources\OrderPallets\OrderPalletsResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderPalletsCollection extends ResourceCollection
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
        return OrderPalletsResource::collection($this->collection);
    }
}