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
                @if($request->reportSchedule)
                    @if( $request->own_report_field)
                        @foreach(json_decode($request->own_report_field)  as $key=>$val)
                        <td>{{$val}}</td>
                        @endforeach
                    @endif
                @else
                    @if($request->own_inventory_report_type)
                        @foreach($request->own_inventory_report_type as $key=>$val)
                            <td>{{$val}}</td>
                        @endforeach
                    @endif
                @endif
            </tr>  
        </thead>
        <tbody>
            @foreach($result as $key => $value)
                <tr>
                    <td>{{isset($value->product->ETIN) ? $value->product->ETIN : ''}}</td>
                    <td>{{isset($value->product->gtin) ? $value->product->gtin : '-'}}</td>
                    <td>{{isset($value->product->upc) ? $value->product->upc : '-'}}</td>
                    <td>{{isset($value->product->product_listing_name) ? $value->product->product_listing_name : '-'}}</td>
                    @if($request->reportSchedule)
                        @if( $request->own_report_field)
                            @foreach(json_decode($request->own_report_field)  as $key=>$val)
                                <td>{{$value->$val}}</td>
                            @endforeach
                        @endif
                    @else
                        @if($request->own_inventory_report_type)
                            @foreach($request->own_inventory_report_type as $val)
                                @if($val== 'gtin' || $val == 'upc' || $val == 'product_listing_name')
                                    <td>{{isset($value->product->$val) ? $value->product->$val : ''}}</td>
                                @else
                                    <td>{{$value->$val}}</td>
                                @endif
                            @endforeach
                        @endif
                    @endif
                </tr>
            @endforeach 
        </tbody>
    </table>
</body>
</html>