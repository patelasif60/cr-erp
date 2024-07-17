<!DOCTYPE html>
<html>
<head>
</head>
<body style="padding:0px">
    <table id="template_table">
        <thead>
            <tr>
                <td>Warehouse</td>
                <td>Client / Supplier</td>
                <td>BOL #</td>
                <td>Date</td>
                <td>Time</td>
                <td>ETIN</td>
                <td>UPC</td>
                <td>GTIN</td>
                <td>Product Listing Name</td>
                <td>Quantity</td>
                @if($request->report_type == 'receive')
                    <td>Total Time to Receive</td>
                    <td>Receiving Time Per Item</td>
                    <td>Received by</td>
                @else
                    <td>Location</td>
                    <td>Location Type</td>
                    <td>User</td>
                @endif
                <td></td>
            </tr>  
        </thead>
        <tbody>
            @foreach($result as $key => $value)
                @if($request->report_type == 'receive')
                    <tr>
                        <td>{{$value->warehouse->warehouses}}</td>
                        <td>{{ $value->purchasingDetail && $value->purchasingDetail->client ? $value->purchasingDetail->client->company_name : '-'}}</td>
                        <td>{{$value->bol_number}}</td>
                        <td></td>
                        <td></td>
                        <td>{{$value->etin}}</td>
                        <td>{{$value->product ? $value->product->upc : '-'}}</td>
                        <td>{{$value->product ? $value->product->gtin : '-'}}</td>
                        <td>{{$value->product ? $value->product->product_listing_name: '-'}}</td>
                        <td>{{$value->qty_received}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    <tr>
                        <td>{{$value->purchasingDetail ? $value->purchasingDetail->warehouse->warehouses : '-'}}</td>
                        <td>{{$value->purchasingDetail && $value->purchasingDetail->client ? $value->purchasingDetail->client->company_name : '-'}}</td>
                        <td>{{$value->bol_number}}</td>
                        <td></td>
                        <td></td>
                        <td>{{$value->etin}}</td>
                        <td>{{$value->product ? $value->product->upc : ''}}</td>
                        <td>{{$value->product ? $value->product->gtin : ''}}</td>
                        <td>{{$value->product ? $value->product->product_listing_name: '-'}}</td>
                        <td>{{$value->quantity}}</td>
                        <td>{{$value->location}}</td>
                        <td>{{$value->masterShelf ? $value->masterShelf->location_type->type: '-'}}</td>
                        <td>{{$value->user ? $value->user->name : '-'}}</td>
                    </tr>
                @endif 
            @endforeach 
        </tbody>
    </table>

</body>
</html>