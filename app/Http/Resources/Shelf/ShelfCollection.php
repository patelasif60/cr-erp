<?php

namespace App\Http\Resources\Shelf;

use App\Http\Resources\Shelf\ShelfResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShelfCollection extends ResourceCollection
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
        return ShelfResource::collection($this->collection);
    }
}