<?php

namespace App\Http\Resources\LocationType;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'location_type'=>$this->type
        ];
    }
}
