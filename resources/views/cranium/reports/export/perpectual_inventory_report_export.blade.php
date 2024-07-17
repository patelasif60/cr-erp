<!DOCTYPE html>
<html>
<head>
</head>
<body style="padding:0px">
    <table id="template_table">
        <thead>
            <tr>
                <td>Item Receipt</td>
                <td>LOB</td>
                <td>Warehouse</td>
                <td>Current Quantity</td>
                <td>Warehouse Location</td>
                <td>SKU</td>
                <td>Received Date</td>
                <td>Received By</td>
                <td>Item Pick Name</td>                                          
            </tr>  
        </thead>
        <tbody>
            @foreach($result as $key => $value)
                <tr>
                    <td>{{$value->ETIN ? $value->ETIN : '-' }}</td>
                    <td>{{$value->product ? $value->product->lobsName():'-'}}</td>
                    <td>{{isset($value->ailse->warehouse_name) ? $value->ailse->warehouse_name->warehouses : '-'}}</td>
                    <td>{{$value->cur_qty}}</td>
                    <td>{{$value->address}}</td>
                    <td>{{$value->product ? $value->product->alternate_ETINs:'-'}}</td>
                    <td></td>
                    <td></td>
                    <td>{{$value->product ? $value->product->product_listing_name : '-'}}</td>
                </tr>
            @endforeach 
        </tbody>
    </table>

</body>
</html>