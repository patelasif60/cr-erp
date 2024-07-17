<?php

namespace App\Http\Resources\Restock;

use Illuminate\Http\Resources\Json\JsonResource;
use DNS2D;
use DNS1D;

class RestockResource extends JsonResource
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
            'address'  => $this->address,
            'pallet_number'  => $this->pallet_number,
            // 'barcodeString' => DNS2D::getBarcodePNG($this->pallet_number, 'PDF417'),
            'barcodeString' => DNS1D::getBarcodePNG($this->pallet_number, 'C128',2,83,array(1,1,0), true),
            'warehouse_id' => $this->warehouse_id,
            'status' => $this->status
        ];
    }
}
