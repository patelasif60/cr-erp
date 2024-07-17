<?php

namespace App\Http\Resources\BackStockPallet;

use App\Http\Resources\BackStockPallet\BackStockPalletResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BackStockPalletCollection extends ResourceCollection
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
        return BackStockPalletResource::collection($this->collection);
    }
}