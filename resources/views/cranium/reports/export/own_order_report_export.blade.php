<!DOCTYPE html>
<html>
<body style="padding:0px">
    <table id="template_table">
        <thead>
            <tr>
                @if($request->reportSchedule)	
                    @if($request->own_report_field)
                        <td>e-tailer Order Number</td>
                        @foreach(json_decode($request->own_report_field)  as $key=>$val)
                            <td>{{$val}}</td>
                        @endforeach
                    @endif
                @else
                    @if($request->own_order_report_type)
                            <td>e-tailer Order Number</td>
                        @foreach($request->own_order_report_type as $key=>$val)
                            <td>{{$val}}</td>
                        @endforeach
                    @endif
                @endif
            </tr>  
        </thead>
        <tbody>
            @foreach($result as $key => $value)
                <tr>
                    @if($request->reportSchedule)
                        @if($request->own_report_field)
                            <td>{{$value->etailer_order_number}}</td>
                            @foreach(json_decode($request->own_report_field)  as $key=>$val)
                                @if($val== 'shipping_label_creation_time' || $val== 'package_num' || $val == 'tracking_number' || $val == 'ship_date')
                                    <td>{{$value->OrderPackage->$val}}</td>
                                @elseif($val == 'order_status_name')
                                    <td>{{$value->status ? $value->status->order_status_name :''}}</td>
                                @elseif($val == 'client')
                                    <td>{{$value->client ? $value->client->company_name:''}}</td>
                                @elseif($val== 'warehouses')
                                    <td>{{ count($value->orderDetail) > 0  ? $value->orderDetail->first()->warehouse : ''}}</td>
                                @elseif($val== 'totalorder')
                                    <td>{{$value->orderDetail->count()}}</td>
                                @else
                                    <td>{{$value->$val}}</td>
                                @endif
                            @endforeach
                        @endif
                    @else
                        @if($request->own_order_report_type)
                            <td>{{$value->etailer_order_number}}</td>
                            @foreach($request->own_order_report_type as $val)
                                @if($val== 'shipping_label_creation_time' || $val== 'package_num' || $val == 'tracking_number' || $val == 'ship_date')
                                    <td>{{$value->OrderPackage->$val}}</td>
                                @elseif($val == 'order_status_name')
                                    <td>{{$value->status ? $value->status->order_status_name :''}}</td>
                                @elseif($val == 'client')
                                    <td>{{$value->client ? $value->client->company_name:''}}</td>
                                @elseif($val== 'warehouses')
                                    <td>{{ count($value->orderDetail) > 0  ? $value->orderDetail->first()->warehouses : ''}}</td>
                                @elseif($val== 'totalorder')
                                    <td>{{$value->orderDetail->count()}}</td>
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