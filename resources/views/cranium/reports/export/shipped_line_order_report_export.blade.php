<!DOCTYPE html>
<html>
<body style="padding:0px">
    <table id="template_table">
        <thead>
            <tr>
                <td>Warehouse</td>
                <td>Client</td>
                <td>e-tailer Order Number</td>
                <td>Channel / Manual Order Number</td>
                <td>Order Date</td>
                <td>Ship to Name</td>
                <td>Ship to Line 1</td>
                <td>Ship to Line 2</td>
                <td>Ship to Line 3</td>
                <td>City</td>
                <td>State</td>
                <td>Zip</td>
                <td>Country</td>
                <td>Must Ship Today</td>
                <td>Transit Time</td>
                @if($request->report_type == 'shipped_line_order')
                <td>SKU</td>
                <td>Item Name</td>
                <td>Quantity</td>
                <td>Tracking Number</td>
                <td>Shipped Date</td>
                <td>Shipped Time</td>
                @else
                <td>Number of Items on the Order</td>
                <td>Number of Packages</td>
                <td>Tracking Number</td>
                <td>Shipped Date</td>
                <td>Shipped Time</td>
                <td>carrier type</td>
                <td>shipment type</td>
                <td>sub order number</td>
                <td>Channel</td>
                <td>shipment temperature</td>
                @endif
                            
            </tr>  
        </thead>
        <tbody>
            @foreach($result as $key => $value)
                <tr>
                    <td>{{$value->warehouse}}</td>
                    @if($request->report_type == 'shipped_line_order')
                    <td>{{$value->orderSummary->client? $value->orderSummary->client->company_name : ''}}</td>
                    <td>{{$value->orderSummary->etailer_order_number}}</td>
                    <td>{{$value->orderSummary->channel_order_number}}</td>
                    <td>{{$value->orderSummary->purchase_date}}</td>
                    <td>{{$value->orderSummary->ship_to_name}}</td>
                    <td>{{$value->orderSummary->ship_to_address1}}</td>
                    <td>{{$value->orderSummary->ship_to_address2}}</td>
                    <td>{{$value->orderSummary->ship_to_address3}}</td>
                    <td>{{$value->orderSummary->ship_to_city}}</td>
                    <td>{{$value->orderSummary->ship_to_state}}</td>
                    <td>{{$value->orderSummary->ship_to_zip}}</td>
                    <td>{{$value->orderSummary->ship_to_country}}</td>
                    <td>{{$value->orderSummary->must_ship_today}}</td>
                    <td>{{$value->transit_days}}</td>
                    <td>{{$value->product ? $value->product->ETIN : ''}}</td>
                    <td>{{$value->product ? $value->product->product_listing_name : ''}}</td>
                    <td>{{$value->quantity_fulfilled}}</td>
                    <td>{{$value->orderPackage() ? $value->orderPackage()->tracking_number :''}}</td>
                    <td>{{$value->orderPackage() ? $value->orderPackage()->ship_date:''}}</td>
                    <td>{{$value->orderPackage()? $value->orderPackage()->shipping_label_creation_time:''}}</td>
                    @else
                    <td>{{$value->client_company_name}}</td>
                    <td>{{$value->etailer_order_number}}</td>
                    <td>{{$value->channel_order_number}}</td>
                    <td>{{$value->purchase_date}}</td>
                    <td>{{$value->ship_to_name}}</td>
                    <td>{{$value->ship_to_address1}}</td>
                    <td>{{$value->ship_to_address2}}</td>
                    <td>{{$value->ship_to_address3}}</td>
                    <td>{{$value->ship_to_city}}</td>
                    <td>{{$value->ship_to_state}}</td>
                    <td>{{$value->ship_to_zip}}</td>
                    <td>{{$value->ship_to_country}}</td>
                    <td>{{$value->must_ship_today}}</td>
                    <td>{{$value->transit_days}}</td>
                    <td>{{$value->shipped_qty}}</td>
                    <td>{{$value->package_num}}</td>
                    <td>{{$value->tracking_number}}</td>
                    <td>{{$value->ship_date}}</td>
                    <td>{{$value->shipping_label_creation_time}}</td>
                    <td>{{$value->company_name}}</td>
                    <td>{{$value->service_name}}</td>
                    <td>{{$value->sub_order_number}}</td>
                    <td></td>
                    @if($value->sub_order_number != null)
                        @php
                            $temp = '';
                            $tempArray = explode('.',$value->sub_order_number);
                            if(isset($tempArray[1]))
                            {
                                if($tempArray[1] == '001'){$temp= 'Frozen';}
                                elseif($tempArray[1] == '002'){$temp = 'Dry';}
                                elseif($tempArray[1] == '003'){$temp = 'Refrigerated';}
                            }
                        @endphp
                    @endif

                    <td>{{$temp}}</td>
                    @endif
                    
                </tr>
            @endforeach 
        </tbody>
    </table>
</body>
</html>