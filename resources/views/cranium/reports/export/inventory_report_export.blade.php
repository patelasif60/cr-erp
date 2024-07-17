<!DOCTYPE html>
<html>
<body style="padding:0px">
    <table id="template_table">
        <thead>
            <tr>	
                <td>ETIN</td>
                <td>UPC</td>
                <td>GTIN</td>
                <td>Product Listing Name</td>
                @if($request->warehouseId == 'NV' || $request->warehouseId == '')
                <td>NV Total Quantity</td>
                <td>NV Orderable Quantity</td>
                <td>NV Fulfillable Quantity</td>
                <td>NV On Order Quantity</td>
                <td>NV Inbound Quantity</td>
                @endif
                @if($request->warehouseId == 'OKC' || $request->warehouseId == '')
                <td>OK Total Quantity</td>
                <td>OK Orderable Quantity</td>
                <td>OK Fulfillable Quantity</td>
                <td>OK On Order Quantity</td>
                <td>OK Inbound Quantity</td>
                @endif
                @if($request->warehouseId == 'WI' || $request->warehouseId == '')
                <td>WI Total Quantity</td>
                <td>WI Orderable Quantity</td>
                <td>WI Fulfillable Quantity</td>
                <td>WI On Order Quantity</td>
                <td>WI Inbound Quantity</td>
                @endif
                @if($request->warehouseId == 'PA' || $request->warehouseId == '')
                <td>PA Total Quantity</td>
                <td>PA Orderable Quantity</td>
                <td>PA Fulfillable Quantity</td>
                <td>PA On Order Quantity</td>
                <td>PA Inbound Quantity</td>
                @endif
            </tr>  
        </thead>
        <tbody>
            @foreach($result as $key => $value)
                <tr>
                    <td>{{$value->ETIN}}</td>
                    <td>{{$value->product ? $value->product->upc : ''}}</td>
                    <td>{{$value->product ? $value->product->gtin : ''}}</td>
                    <td>{{$value->product ? $value->product->product_listing_name: ''}}</td>
                    @if($request->warehouseId == 'NV' || $request->warehouseId == '')
                    <td>{{ $value->nv_qty > 0 ? $value->nv_qty  : 0 }}</td>
                    <td>{{$value->nv_orderable_qty > 0 ? $value->nv_orderable_qty : 0 }}</td>
                    <td>{{$value->nv_fulfilled_qty > 0 ? $value->nv_fulfilled_qty : 0 }}</td>
                    <td>{{$value->nv_open_order_qty > 0 ? $value->nv_open_order_qty : 0 }}</td>
                    <td>{{$value->nv_each_qty > 0 ? $value->nv_each_qty : 0 }}</td>
                    @endif
                    @if($request->warehouseId == 'OKC' || $request->warehouseId == '')
                    <td>{{$value->okc_qty > 0 ? $value->okc_qty : 0 }}</td>
                    <td>{{$value->okc_orderable_qty > 0 ? $value->okc_orderable_qty : 0 }}</td>
                    <td>{{$value->okc_fulfilled_qty > 0 ? $value->okc_fulfilled_qty : 0 }}</td>
                    <td>{{$value->okc_open_order_qty > 0 ? $value->okc_open_order_qty : 0 }}</td>
                    <td>{{$value->okc_each_qty > 0 ? $value->okc_each_qty : 0 }}</td>
                    @endif
                    @if($request->warehouseId == 'WI' || $request->warehouseId == '')
                    <td>{{ floatval($value->wi_qty) > 0 ? $value->wi_qty : 0 }}</td>
                    <td>{{$value->wi_orderable_qty > 0 ? $value->wi_orderable_qty : 0 }}</td>
                    <td>{{$value->wi_fulfilled_qty > 0 ? $value->wi_fulfilled_qty: 0 }}</td>
                    <td>{{$value->wi_open_order_qty > 0 ? $value->wi_open_order_qty : 0 }}</td>
                    <td>{{$value->wi_each_qty > 0 ? $value->wi_each_qty : 0 }}</td>
                    @endif
                    @if($request->warehouseId == 'PA' || $request->warehouseId == '')
                    <td>{{$value->pa_qty > 0 ? $value->pa_qty : 0}}</td>
                    <td>{{$value->pa_orderable_qty > 0 ? $value->pa_orderable_qty : 0 }}</td>
                    <td>{{$value->pa_fulfilled_qty > 0 ? $value->pa_fulfilled_qty : 0 }}</td>
                    <td>{{$value->pa_open_order_qty > 0 ? $value->pa_open_order_qty : 0 }}</td>
                    <td>{{$value->pa_each_qty > 0 ? $value->pa_each_qty : 0 }}</td>
                    @endif
                </tr>
            @endforeach 
        </tbody>
    </table>
</body>
</html>