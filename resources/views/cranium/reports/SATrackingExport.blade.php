<table>
    <tr>
        <th>OrderId</th>
        <th>sku</th>
        <th>qtyShipped</th>
        <th>carrier</th>
        <th>trackingNumber</th>
        <th>shipMethod</th>
        <th>packageCode</th>
        <th>quantityCancelled</th>
        <th>cancellationReason</th>
        <th>shippedDate</th>
        <th>shippingCost</th>
    </tr>
    @if($package)
        @foreach($package as $row_package)
            <tr>
                <td>{{$orderSummary->sa_order_number}}</td>
                @php
                    $orderDetail = \App\OrderDetail::where('ETIN', $row_package->ETIN)->where('sub_order_number', $row_package->order_id)->first();
                @endphp
                @if($orderDetail)
                <td>{{$orderDetail->SA_sku}}</td>    
                @else
                <td>-</td>
                @endif
                <td>{{$row_package->shipped_qty}}</td>
                <td>{{(isset($order_info->carrier->company_name) ? $order_info->carrier->company_name : '')}}</td>
                <td>{{$row_package->tracking_number}}</td>
                <td>{{(isset($row_package->carrier_service->service_name) ? $row_package->carrier_service->service_name : '')}}</td>
                <td>{{$row_package->package_num}}</td>
                <td></td>
                <td></td>
                <td>{{date('m/d/Y',strtotime($row_package->ship_date))}}</td>
                <td></td>
            </tr>
        @endforeach
    @endif
    
</table>