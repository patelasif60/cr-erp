<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
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
        return ProductResource::collection($this->collection);
    }
}