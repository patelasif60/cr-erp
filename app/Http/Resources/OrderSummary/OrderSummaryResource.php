<?php

namespace App\Http\Resources\OrderSummary;

use Illuminate\Http\Resources\Json\JsonResource;
class OrderSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(isset($this->client->company_name)) {
            $customer_name =  $this->client->company_name;
         } else{
            $customer_name = '';
        }
        return [
            'id'        => $this->id,
            'customer_name'     => $customer_name,
            'ship_to_name'     => $this->ship_to_name,
            'ship_to_address1'     => $this->ship_to_address1,
            'ship_to_address2'     => $this->ship_to_address2,
            'ship_to_city'     => $this->ship_to_city,
            'ship_to_state'     => $this->ship_to_state,
            'ship_to_zip'     => $this->ship_to_zip,
            'must_ship_today'     => $this->must_ship_today == 1 ? 'Yes': 'No',
            'must_ship_today_status' => $this->must_ship_today
        ];
    }
}
