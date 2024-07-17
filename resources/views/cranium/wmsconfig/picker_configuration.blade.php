    <div class="breadcrumb">
    <div class="form-group col-12">
        <h3>Picker Configuration </h3>
    </div>
    <div class="form-group col-4">
        <label for="warehouses" class="ul-form__label">Warehouse<span class="text-danger">*</span></label>
        <select id="pickwarehouses" onchange="pickerconfi()" name="pickwarehouses" class="form-control select2" >
                <option>Select warehouse</option>            
             @foreach($warehouses as $warehouse)
                <option value="{{$warehouse->id}}">{{$warehouse->warehouses}}</option>
            @endforeach
        </select>
    </div> 
</div>

    <table id="picker_datatable" class="d-none display table table-striped table-bordered js-picker-conf" style="width:100%">
        <thead>
            <tr>
                <th >Picker</th>
                <th >Temperature</th>
                <th >Batch Max (Until 2PM) </th>
                <th >Batch Max (2PM-4PM)</th>
                <th >Batch Max (After 4PM)</th>
                <th >Action</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
    <div class="modal fade" id="MyModalPicker" data-backdrop="static">
    </div>
