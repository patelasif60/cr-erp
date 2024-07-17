<div class="modal-header" style="background-color:#fff;">
    <h3 id="hmodelHeader"></h3>
    <!--Close/Cross Button-->
     <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
</div> 
<form  method="POST" data-form="add" action="javascript:void(0)" id="edit_form" >
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="group_name" class="ul-form__label">ETIN</label>
                <select name="ETIN" id="etin" class="form-control">
                    <option value="">Select ETIN</option>
                    @foreach ($etin as $key=>$row_etin2)
                    <option {{$productinventory->ETIN == $row_etin2 ? 'selected' : ''}} value="{{ $row_etin2 }}">{{ $row_etin2 }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-12">
                <label for="warehouses_assigned" class="ul-form__label">Warehouse</label>
                <table class="table">
                    @if ($warehouses)
                        @foreach($warehouses as $key=>$warehouse)
                            <tr>
                                <td>{{ $warehouse }}</td>
                                <td><input   {{ $productinventory->warehouse_id == $key?'checked':'disabled' }} type="checkbox" name="warehouses[]" value="{{$key}}"></td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>
            <input type="hidden" name="id" id="id" value="{{$productinventory->id}}">
            <div class="form-group col-md-12">
                <label for="inventory" class="ul-form__label">Inventory</label>
                <input class="form-control" type="number" name="inventory" id="inventory" min="0" step="1" value="{{$productinventory->inventory}}">
            </div>
        </div> 
    </div>
    <div class="modal-footer"> 
        <button type="button"  class="edit-form btn btn-primary btn-txt">save</button> 
        <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
    </div>
</form>