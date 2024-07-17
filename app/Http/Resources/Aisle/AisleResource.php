<?php

namespace App\Http\Resources\Aisle;

use Illuminate\Http\Resources\Json\JsonResource;

class AisleResource extends JsonResource
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
            'location_name'=>$this->location_name,
            'warehouse_id' => $this->warehouse_id,
            'warehouse_name' => $this->warehouse_name->warehouses,
            'aisle_name'=>$this->aisle_name,
            'product_temp_id'=>$this->product_temp_id,
            'product_temperature' => isset($this->storage_type) && ($this->storage_type->group_name) ? $this->storage_type->group_name : ""
        ];
    }
}
