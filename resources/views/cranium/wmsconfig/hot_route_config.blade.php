<div class="breadcrumb">
    <div class="form-group col-12">
        <h3>Picker Configuration </h3>
    </div>
    <div class="row col-md-12">
        <div class="form-group col-md-3">
            <label for="wh_td" class="ul-form__label">Warehouse<span class="text-danger">*</span></label>
            <select id="wh_td" name="wh_td" class="form-control select2" required >
                <option value="">Select Warehouse</option>            
                @foreach($warehouses as $warehouse)
                    <option value="{{$warehouse->id}}">{{$warehouse->warehouses}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="carrier_type" class="ul-form__label">Carrier<span class="text-danger">*</span></label>
            <select id="carrier_type" name="carrier_type" class="form-control select2" required>
                <option value="">Select Carrier</option>  
                @foreach($carriers as $carrier)
                    <option value="{{$carrier->id}}">{{$carrier->company_name}}</option>
                @endforeach            
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="t_day" class="ul-form__label">Transit Day(s)<span class="text-danger">*</span></label>
            <input name="t_day" id="t_day" class="form-control" required />
        </div>
        <div class="form-group col-md-3">
            <label for="cut_off_time" class="ul-form__label">Cut-Off Time</label>
            <?php
                $time_slots = create_time_range('00:00', '23:30', '30 mins'); 
            ?>
            <select id="cut_off_time" name="cut_off_time" class="form-control select2">
                <option value="">Select Cut-Off</option>  
                @foreach($time_slots as $ts)
                    <option value="{{$ts}}">{{$ts}}</option>
                @endforeach            
            </select>
        </div>
    </div>
    <div class="form-group col-md-6">
        <label for="zip_codes" class="ul-form__label">Zip Code(Comma seperated for Multiple)<span class="text-danger">*</span></label>
        <textarea name="zip_codes" id="zip_codes" class="form-control" required></textarea>
    </div>
    <div>
        <button type="button" class="btn btn-success mt-5" onclick="saveHotRoute();">Save</button>
    </div>
</div>
<hr>
<h4>Hot Route List</h4>
<table id="hot_route_datatable" class="display table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th></th>
            <th>Warehouse</th>
            <th>Carrier</th>
            <th>Zip</th>
            <th>Transit Day</th>            
            <th>Cut-Off Time</th>            
            <th>
                Action
                <button class="btn btn-danger" onclick="deleteRoute('all')">Delete All</button>
                <button class="btn btn-danger" id="sel_del" onclick="deleteSelected()" disabled>Delete Selected</button>
            </th>
        </tr>
    </thead>
    <tbody>        
    </tbody>
</table>
