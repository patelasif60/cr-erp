<?php

namespace App\Http\Resources\OrderPallets;

use Illuminate\Http\Resources\Json\JsonResource;
use DNS2D;
use DNS1D;

class OrderPalletsResource extends JsonResource
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
            'pallet_number'  => $this->pallet_number,
            'barcodeString' => DNS1D::getBarcodePNG($this->pallet_number, 'C128',2,83,array(1,1,0), true),
            'status' => $this->status,
            'po_number' => $this->po_number,
            'bol_number' => $this->bol_number
        ];
    }
}
