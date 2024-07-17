<!DOCTYPE html>
<html>
<body style="padding:0px">
    @if($fieldname)
    <table id="template_table">
        <thead>
            <tr>
                @foreach($fieldname as $val)
                    @if($request->report_type == "all")
                        <th class="table_border">{{$val}}</th>
                    @else
                        @if($request->reportSchedule)
                            @if( $request->own_report_field && in_array($val,json_decode($request->own_report_field)))
                                <th class="table_border">{{$val}}</th>
                            @endif
                        @else
                            @if($request->columns && in_array($val,$request->columns))
                                <th class="table_border">{{$val}}</th>
                            @endif
                        @endif
                    @endif
                @endforeach                                          
            </tr>  
        </thead>
        <tbody>
            @foreach($result as $key => $value)
                <tr>
                    @foreach($fieldname as $val)
                        @if($request->report_type == "all")
                            @if($val == 'lobs')
                                <td>{{$value->lobsName}}</td>
                            @elseif($val == 'product_category')
                                <td>{{$value->category}}</td>
                            @elseif($val == 'product_subcategory1')
                                <td>{{$value->sub_category_1}}</td>
                            @elseif($val == 'product_subcategory2')
                                <td>{{$value->sub_category_2}}</td>
                            @elseif($val == 'product_subcategory3')
                                <td>{{$value->sub_category_3}}</td>
                            @elseif($val == 'product_tags')
                                <td>{{$value->tagsName}}</td>
                            @elseif($val == 'allergens')
                                <td>{{$value->allergensName}}</td>
                            @else
                                <td>{{$value->$val}}</td>
                            @endif
                        @else
                            
                            @if($request->reportSchedule)
                                @if( $request->own_report_field && in_array($val,json_decode($request->own_report_field)))
                                    @if($val == 'lobs')
                                        <td>{{$value->lobsName}}</td>
                                    @elseif($val == 'product_category')
                                        <td>{{$value->category}}</td>
                                    @elseif($val == 'product_subcategory1')
                                        <td>{{$value->sub_category_1}}</td>
                                    @elseif($val == 'product_subcategory2')
                                        <td>{{$value->sub_category_2}}</td>
                                    @elseif($val == 'product_subcategory3')
                                        <td>{{$value->sub_category_3}}</td>
                                    @elseif($val == 'product_tags')
                                        <td>{{$value->tagsName}}</td>
                                    @elseif($val == 'allergens')
                                        <td>{{$value->allergensName}}</td>
                                    @else
                                        <td>{{$value->$val}}</td>
                                    @endif
                                @endif
                            @else
                                @if($request->columns && in_array($val,$request->columns))
                                    @if($val == 'lobs')
                                        <td>{{$value->lobsName}}</td>
                                    @elseif($val == 'product_category')
                                        <td>{{$value->category}}</td>
                                    @elseif($val == 'product_subcategory1')
                                        <td>{{$value->sub_category_1}}</td>
                                    @elseif($val == 'product_subcategory2')
                                        <td>{{$value->sub_category_2}}</td>
                                    @elseif($val == 'product_subcategory3')
                                        <td>{{$value->sub_category_3}}</td>
                                    @elseif($val == 'product_tags')
                                        <td>{{$value->tagsName}}</td>
                                    @elseif($val == 'allergens')
                                        <td>{{$value->allergensName}}</td>
                                    @else
                                        <td>{{$value->$val}}</td>
                                    @endif
                                @endif
                            @endif
                        @endif
                    @endforeach
                </tr> 
            @endforeach 
        </tbody>
    </table>
    @else

    @endif
</body>
</html>