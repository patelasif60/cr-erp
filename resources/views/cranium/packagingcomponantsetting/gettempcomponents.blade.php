@foreach($tempComponents as $tempComponentsKey => $tempComponentsVal)
    <tr id="{{$tempComponentsVal->PackagingMaterials->id}}">
        <td>{{$tempComponentsVal->PackagingMaterials->ETIN}}</td>
        <td>{{$tempComponentsVal->PackagingMaterials->product_description}}</td>
        <td>
            <input type="number" class="form-control" name="components[{{$tempComponentsVal->PackagingMaterials->id}}]" value="{{$tempComponentsVal->qty}}" id="components_qty" style="width:55px;padding:0px">
        </td>
        <td>
            <a href="javascript:void(0)" class="btn btn-danger" onClick="removeProduct({{$tempComponentsVal->PackagingMaterials->id}})">Delete</a>
        </td>

    </tr>
@endforeach
<input type="hidden" id="selectedPack" name="selectedPack" value="{{$response['selectedData']}}">