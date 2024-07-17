<form method="POST" action="javascript:void(0)" id="edit_shipping_elegibility_form">
<div class="breadcrumb">
    <div class="form-group col-12">
        <h3>Order Day Shipping Eligibility </h3>
    </div>
    <div class="form-group col-4">
        <label for="warehouses" class="ul-form__label">Warehouse<span class="text-danger">*</span></label>
        <select id="warehouses" onchange="shippingElgibility()" name="warehouses" class="form-control select2" >
                <option>Select warehouse</option>            
             @foreach($warehouses as $warehouse)
                <option value="{{$warehouse->id}}">{{$warehouse->warehouses}}</option>
            @endforeach
        </select>
    </div> 
</div>
<table id="order_shipping_eligibility_table" class="js-shipping d-none display table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th >Monday</th>
            <th >Tuesday</th>
            <th >Wednesday</th>
            <th >Thursday</th>
            <th >Friday</th>
        </tr>
    </thead>
    <tbody id="myshipel">
     
    </tbody>
</table>
<!-- <button  type="submit" class="js-shipping btn btn-primary d-none" >Update</button>  -->
</form>
