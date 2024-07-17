<!DOCTYPE html>
<html>

<body>
    <table>
        <thead>
            <tr>
                <td>Order No</td>
                <td>Customer Order No</td>
                <td>Client</td>
                <td>Warehouse</td>
                <td>Order Date</td>
                <td>Carrier</td>	
                <td>Bill To Email</td>
                <td>Number of Cartons</td>
                <td>Line Items</td>
                <td>Alternate ETIN</td>
                <td>Order Message</td>
                <td>Shipped Date</td>
                <td>Ship to</td>
                <td>Ship to State</td>
                <td>Status</td>
                <td>Tracking Number</td>
                <td>Transit Time</td>
                <td>order type</td>
                <td>store</td>
                <td>customer Note</td>
            </tr>  
        </thead>
        <tbody>
            @foreach($result as $key => $value)
                <tr>
                    <td>{{$value->etailer_order_number}}</td>
                    <td>{{$value->channel_order_number}}</td>
                    <td>{{$value->client_company_name}}</td>
                    <td>{{$value->warehouse}}</td>
                    <td>{{date("m/d/Y H:i:s", strtotime($value->purchase_date)) }}</td>
                    <td>{{$value->company_name}}  {{$value->service_name}}</td>
                    <td>{{$value->customer_email}}</td> 
                    <td>{{$value->packageTotal}}</td> 
                    <td>
                        @php
                            $array = explode(",", $value->ETIN);
                        @endphp
                        @foreach($array as $etinval)
                            {{$etinval }}<br/>
                        @endforeach
                    </td>
                    <td>
                        @php
                            $altarray = explode(",", $value->alternate_ETINs);
                        @endphp
                        @foreach($altarray as $altetinval)
                            {{ $altetinval }} <br/>
                        @endforeach
                    </td>
                    <td></td>
                    <td>{{date("m/d/Y", strtotime($value->ship_date)) }}</td>
                    <td>{{$value->ship_to_name}}
                        <br>{{$value->ship_to_address1}},
                        {{$value->ship_to_address2}}
                        {{$value->ship_to_address3}}<br>
                        {{$value->ship_to_city}},{{$value->ship_to_state}},{{$value->ship_to_zip}}
                     </td>
                    <td>{{$value->ship_to_state}}</td>
                    <td>{{$value->status == 6 ?'Shipped' :'Reship Shipped'}}</td>
                    <td>{{$value->tracking_number}}</td>
                    <td>{{$value->transit_days}}</td>
                    
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
					<td>{{$value->channel_type}}</td>
                </tr>
            @endforeach 
        </tbody>
    </table>
</body>
</html>