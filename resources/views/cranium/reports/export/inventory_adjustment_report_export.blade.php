<!DOCTYPE html>
<html>
<head>
</head>
<body style="padding:0px">
    <table id="template_table">
        <thead>
            <tr>
                <td>Warehouse</td>
                <td>ETIN</td>
                <td>UPC</td>
                <td>GTIN</td>
                <td>Product Listing Name</td>
                <td>Location</td>
                <td>Location Type</td>
                <td>Starting Inventory</td>
                <td>Ending Inventory</td>
                <td>Total Change</td>
                <td>Date</td>
                <td>Time</td>
                <td>User</td>
            </tr>  
        </thead>
        <tbody>
            @foreach($result as $key => $value)
                <tr>
                    <td>{{$value->warehouse ? $value->warehouse : '-'}}</td>
                    <td>{{$value->ETIN}}</td>
                    <td>{{$value->upc}}</td>
                    <td>{{$value->gtin}}</td>
                    <td>{{$value->product_listing_name}}</td>
                    <td>{{$value->location}}</td>
                    <td>{{$value->type}}</td>
                    <td>{{$value->starting_qty}}</td>
                    <td>{{$value->ending_qty}}</td>
                    <td>{{$value->total_change}}</td>
                    <td></td>
                    <td></td>
                    <td>{{ $value->name}}</td>
                </tr>
            @endforeach 
        </tbody>
    </table>
</body>
</html>