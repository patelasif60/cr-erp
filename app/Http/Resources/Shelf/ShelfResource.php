<?php

namespace App\Http\Resources\Shelf;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Aisle\AisleResource;
use App\Http\Resources\LocationType\LocationTypeResource;
use App\Http\Resources\Bay\BayResource;
use App\Http\Resources\Shelf\ShelfResource;
use App\Http\Resources\Product\ProductResource;
use DNS2D;
use DNS1D;


class ShelfResource extends JsonResource
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
            'id'        => $this->id,
            'shelf'     => $this->shelf,
            'address'   => $this->address,
            'slot'      => $this->slot,
            'ETIN'      => $this->ETIN,
            'max_qty'   => $this->max_qty,
            'cur_qty'   => $this->cur_qty,
            'location_type'=> new LocationTypeResource($this->location_type),
            'bay_id' => $this->bay_id,
            'aisle_id' => $this->aisle_id,
            'location_type_id' =>$this->location_type_id,
            'bay'       => new BayResource($this->bay_name),
            'barcodeString' => DNS1D::getBarcodePNG($this->address, 'C128',2,83,array(1,1,0), true),
            'parent_id' => $this->parent_id,
            'child' => ShelfResource::collection($this->whenLoaded('child')),
            'product' => new ProductResource($this->product),
            'processing_group_name' => $this->group_name,
            'pallet_number' => $this->pallet_number
        ];
    }
}
