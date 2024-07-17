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