<!DOCTYPE html>
<html>
<head>
    <title>Cranium</title>
<style>

    @page { size: 100.0cm 100.0cm;  }   

    #master_product_table {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #master_product_table td, #master_product_table th {
        border: 1px solid #ddd;
        padding: 8px 0px;
        /* width : 5%; */
    }

    #master_product_table tr:nth-child(even){background-color: #f2f2f2;}

    #master_product_table tr:hover {background-color: #ddd;}

    #master_product_table th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #8dc4ff;
        /* color: white; */
    }
</style>
</head>
<body>
    <table id="master_product_table">
        <thead>
            <tr>
                @if($filter_val)
                    @foreach($filter_val as  $key => $filter)
                        @if(!empty($visible_columns) && in_array(json_decode($filter['info'],true)['sorting_order'],$visible_columns))
                        <th scope="col">{{ json_decode($filter['info'],true)['label_name'] }}</th>
                        @endif

                        @if(empty($visible_columns))
                        <th scope="col">{{ json_decode($filter['info'],true)['label_name'] }}</th>
                        @endif
                    @endforeach
                    <th>Image url</th>
                @endif
            </tr>   
        </thead>
        <tbody>
            @if($product_data)
                @foreach($product_data as $master_product)
                <tr>
                    @foreach($filter_val as  $key => $filter)
                        @if(!empty($visible_columns) && in_array(json_decode($filter['info'],true)['sorting_order'],$visible_columns))
                            @if($key == 'product_tags')
                                <td scope="col">{{ $mastrpro->productTag($master_product->$key) }}</td>
                            @else
                                <td scope="col">{{ $master_product->$key }}</td>
                            @endif

                        @endif

                        @if(empty($visible_columns))
                            @if($key == 'product_tags')
                                <td scope="col">{{ $mastrpro->productTag($master_product->$key) }}</td>
                            @else
                                <td scope="col">{{ $master_product->$key }}</td>
                            @endif
                        @endif
                    @endforeach
                    <td scope="col">{{ $mastrpro->productImage($master_product->ETIN) }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

</body>
</html>