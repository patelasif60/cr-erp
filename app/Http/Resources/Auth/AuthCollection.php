<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\Auth\AuthResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AuthCollection extends ResourceCollection
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
        return AuthResource::collection($this->collection);
    }
}