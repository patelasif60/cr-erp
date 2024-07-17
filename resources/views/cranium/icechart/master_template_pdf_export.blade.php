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
                <th class="table_border">Transit</th>
                <th class="table_border" colspan="2" style="background-color:#92D050">1 Day</th>
                <th class="table_border" colspan="2" style="background-color:#00B0F0">2 Day</th>
                <th class="table_border" colspan="2" style="background-color:#FFC000">3 Day</th>
                <th class="table_border" colspan="2" style="background-color:#FFFF00">4 Day</th>                                          
            </tr>
            <tr>
                <td class="table_border">Box#</td>
                <td class="table_border">Block</td>
                <td class="table_border">Pellet</td>
                <td class="table_border">Block</td>
                <td class="table_border">Pellet</td>
                <td class="table_border">Block</td>
                <td class="table_border">Pellet</td>
                <td class="table_border">Block</td>
                <td class="table_border">Pellet</td>
            </tr>  
        </thead>
        <tbody>
           @foreach($iceEditArray as $key=>$val)
                @if(isset($result[$val->packaging_materials_id]))
                    @if(isset($result[$val->packaging_materials_id])
                        && ( $val['1day_block']!= ''
                        || $val['1day_pellet']!= ''
                        || $val['2day_block']!= ''
                        || $val['2day_pellet']!= ''
                        || $val['3day_block']!= ''
                        || $val['3day_pellet']!= ''
                        || $val['4day_block']!= ''
                        || $val['4day_pellet']!= ''
                        ))
                        <tr>
                            <td class="table_border">{{$result[$val['packaging_materials_id']]}}</td>
                            <td class="table_border">
                                {{$val['1day_block']}}
                            </td>
                            <td class="table_border">
                               {{$val['1day_pellet']}}
                            </td>
                            <td class="table_border">
                                {{$val['2day_block']}}
                            </td>
                            <td class="table_border">
                                {{$val['2day_pellet']}}
                            </td>
                            <td class="table_border">
                               {{$val['3day_block']}}
                            </td>
                            <td class="table_border">
                                {{$val['3day_pellet']}}
                            </td>
                            <td class="table_border">
                                {{$val['4day_block']}}
                            </td>
                            <td class="table_border">
                                {{$val['4day_pellet']}}
                            </td>
                        </tr>
                    @endif
                @endif
            @endforeach
        </tbody>
    </table>

</body>
</html>