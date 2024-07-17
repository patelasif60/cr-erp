<?php

namespace App\Http\Resources\Bay;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Aisle\AisleResource;

class BayResource extends JsonResource
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
            'aisle_id' =>  $this->aisle_id,
            'type' =>  $this->type,
            'aisle_name'=> new AisleResource($this->aisle_name),
            'shelf_count' =>$this->shelfs->count(),
            'bay_number' => $this->bay_number,
            'parent_id' => $this->parent_id
        ];
    }
}
