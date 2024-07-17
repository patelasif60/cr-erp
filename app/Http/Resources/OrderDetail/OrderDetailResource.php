<?php

namespace App\Http\Resources\OrderDetail;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\OrderSummary\OrderSummaryResource;
use App\OrderDetail;
use App\MasterProduct;
use DNS2D;
use DNS1D;


class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function productmultiple($subOrderNumber)
    {
        
    }
    public function toArray($request)
    {
        $etin = OrderDetail::where('sub_order_number',$this->sub_order_number)->whereIn('status',[1,2])->pluck('ETIN')->toArray();
        $product = MasterProduct::whereIn('ETIN',$etin)->get();
        return [
            'id'        => $this->id,
            'sub_order_number' => $this->sub_order_number,
            'product'=>  ProductResource::collection($product),
            'summery_detail'=> new OrderSummaryResource($this->orderSummary),
            // 'barcodeString' => DNS2D::getBarcodePNG($this->sub_order_number, 'PDF417'),
            'barcodeString' =>  DNS1D::getBarcodePNG($this->sub_order_number, 'C128',2,83,array(1,1,0), true),
        ];
    }
}
