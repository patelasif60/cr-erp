<!DOCTYPE html>
<html>
<head>
    <title>Cranium</title>
<style>

    @page { size: 30.0cm auto;  }   

    #template_table {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #template_table td, #template_table th {
        border: 1px solid #ddd;
        /* padding: 8px 0px; */
        /* width : 5%; */
    }

    #template_table tr:nth-child(even){background-color: #f2f2f2;}

    #template_table tr:hover {background-color: #ddd;}

    #template_table th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #8dc4ff;
        /* color: white; */
    }
</style>
</head>
<body style="padding:0px">
    <table id="template_table">
        <thead>
            <tr>
                <td>ETIN</td>
                <td>UPC</td>
                <td>GTIN</td>
                <td>Product Listing Name</td>
                <td>quantity</td>
                <td>Starting Location</td>
                <td>Ending Location</td>
                <td>Start Time</td>
                <td>End Time</td>
                <td>User</td>
            </tr>  
        </thead>
        <tbody>
            @foreach($result as $key => $val)
                @foreach($val->restockItemwithETIN as $value)
                @if($request->client_id == '')
                    <tr>
                        <td>{{$value->ETIN}}</td>
                        <td>{{$value->products->upc}}</td>
                        <td>{{$value->products->gtin}}</td>
                        <td>{{$value->products->product_listing_name}}</td>
                        <td>{{$value->quantity}}</td>
                        <td>{{$value->from_location}}</td>
                        <td>{{$value->to_location}}</td>
                        <td></td>
                        <td></td>   
                        @if($request->reportSchedule)
                            <td></td>
                        @else
                        <td>{{Auth::user()->name}}</td>
                        @endif
                    </tr>
                @else
                    @if(in_array($request->client_id,explode(",",$value->products->lobs)))
                    <tr>
                        <td>{{$value->ETIN}}</td>
                        <td>{{$value->products->upc}}</td>
                        <td>{{$value->products->gtin}}</td>
                        <td>{{$value->products->product_listing_name}}</td>
                        <td>{{$value->quantity}}</td>
                        <td>{{$value->from_location}}</td>
                        <td>{{$value->to_location}}</td>
                        <td></td>
                        <td></td>
                        @if($request->reportSchedule)
                            <td></td>
                        @else
                        <td>{{Auth::user()->name}}</td>
                        @endif
                    </tr>
                    @endif
                @endif
                @endforeach 
            @endforeach 
        </tbody>
    </table>
</body>
</html>