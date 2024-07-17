<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id'    => $this->id,
            'ETIN'  => $this->ETIN,
            'upc'  => $this->upc,
            'parent_ETIN'  => $this->parent_ETIN,
            'product_listing_name'  => $this->product_listing_name,
            'full_product_desc'  => $this->full_product_desc,
            'about_this_item'  => $this->about_this_item,
            'product_type'  => $this->product_type,
            'shelf_total_qty'=> $this->masterShelf->where('location_type_id',1)->sum('cur_qty'),
            'shelf_address' => $this->masterShelf->first() ? $this->masterShelf->first()->address : '',
            'client_supplier' => ($this->supplier_type == 'supplier' ? SupplierName($this->client_supplier_id) : clientName($this->client_supplier_id)),
            'client_supplier_id' => $this->client_supplier_id,
            'warehouses_assigned' => $this->warehouses_assigned
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->,
            // ''  => $this->

        ];
    }
}
