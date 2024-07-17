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
                <td>Order Status</td>
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
                <td>Number of Items on the Order</td>
                @if($request->report_type == 'shipped_order' || $request->report_type == 'all_order')
                <td>Number of Packages</td>
                <td>Tracking Number</td>
                <td>Shipped Date</td>
                <td>Shipped Time</td>
                @endif
            </tr>  
        </thead>
        <tbody>
            @foreach($result as $key => $value)
                <tr>
                    {{-- dd($value->orderDetail) --}}
                    @if($request->warehouseId != '')
                    <td>{{ count($value->orderDetail) > 0  ? $value->orderDetail->where('warehouse',$request->warehouseId)->first()->warehouse :'-'}}</td>
                    @else
                    <td>{{ count($value->orderDetail) > 0  ? $value->orderDetail->first()->warehouse : '-'}}</td>
                    @endif
                    <td>{{$value->client ? $value->client->company_name:''}}</td>
                    <td>{{$value->etailer_order_number}}</td>
                    <td>{{$value->channel_order_number}}</td>
                    <td>{{$value->status ? $value->status->order_status_name :'-'}}</td>
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
                    <td>{{$value->orderDetail ? $value->orderDetail->sum('transit_days') : '-'}}</td>
                    <td>{{$value->orderDetail ? $value->orderDetail->sum('quantity_ordered') : '-'}}</td>
                    @if($request->report_type == 'shipped_order' || $request->report_type == 'all_order')
                    <td>{{$value->OrderPackage ? count($value->OrderPackage->groupBy('id')) : '-'}}</td>
                    <td>{{$value->OrderPackage ? implode(",",array_unique($value->OrderPackage->pluck('tracking_number')->toArray())) : '-' }}</td>
                    <td>{{$value->OrderPackage ? implode(",",array_unique($value->OrderPackage->pluck('ship_date')->toArray())) : '-'}}</td>
                    <td>{{$value->OrderPackage ?  implode(",",array_unique($value->OrderPackage->pluck('shipping_label_creation_time')->toArray())) : '-'}}</td>
                    @endif
                </tr>
            @endforeach 
        </tbody>
    </table>
</body>
</html>