@extends('layouts.master')
@section('before-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/dropzone.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">

<style>
  
form .form-group {
    margin-right: -5px;
    display: inline-block;
}

div.marqueeclass {
    color: red;
    height: 50px;
    font-weight: 900;
    font-size: 25px;
    padding-top: 5px;
    word-spacing: 5px;
	text-align:center;
}
div.sucessmarqueeclass {
    color: #285b2a;
    height: 50px;
    font-weight: 900;
    font-size: 25px;
    padding-top: 5px;
    word-spacing: 5px;
	text-align:center;
}
form .form-group {
    margin-right: -5px;
    display: inline-block;
}
.form-group.col-md-12.custom-imgdiv img {
    height: 100%;
    width: 100%;
	padding-top: 10px;
	padding-bottom: 10px;
}
.col-md-3.column.single-image {
    padding: 10px 0px 40px 25px;
	flex: 4 4 20%;
}
.edited-field {
    border: 1px solid #C0FF00 !important;
}

</style>
@endsection

@section('main-content')

<form method="POST" action="#" enctype="multipart/form-data" id="product_add">
	<input type="hidden" value="{{ $row->is_approve }}" name='is_approve' id='is_approve'>
	<input type="hidden" value="{{ $row->approved_date }}" name='approved_date' id='approved_date'>
	
	@csrf
<div class="card">
	<div class="card-header bg-transparent">
		<div class="row">
			<div class="col-md-6">
				<h3 class="card-title"> Edit Kit Product</h3>
			</div>
			<div class="col-md-6">
			
			<button type="submit" class="btn btn-primary btn-icon m-1" style="float: right; text-align: right;" id="btnApprove"><span class="ul-btn__icon"><i class="i-Gear-2"></i></span> Approved</button>	
			<div class="btn btn-primary btn-icon m-1"  style="float: right; text-align: right;">
				<span class="ul-btn__icon"><i class="i-Gear-2"></i></span>
				<a href="{{ url('ApproveOrRejectProductRequest/'.$row->ETIN) }}/0" class="ul-btn__text" style="color:#fff;"> Reject </a>
			</div>
			</div>
		</div>
		@if($row->is_approve == 0)
			<a href="#" id="updateflag"><div direction="left" class="marqueeclass">This Product is not approved yet. Click Here  to approve </div></a>
		@endif
		@if($row->is_approve == 1)
			<div direction="left" class="sucessmarqueeclass">This Product is LIVE. </div>
		@endif
		<div id="error_container"></div>
	</div>

<!--begin::form-->
	
	<input type="hidden" name="item_form_description" id="item_form_description" value="Kit" >
		<div class="card-body ">
			<!----------===================================================------------>
			<div class="row col-lg-12">
				<div class="col-lg-8">
					<div class="form-group col-md-12">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="The product name displayed on stores and marketplaces">Product Listing Name<small>(Auto Generated)</small></label>
						<input type="text" class="form-control" id="product_listing_name" name="product_listing_name" placeholder="Product Listing Name" value="{{ $row->product_listing_name }}" readonly>
					
					</div>
					<div class="form-group col-md-6">
						<label for="ETIN" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="e-tailer internal SKU">ETIN</label>
						<input type="text" class="form-control" id="ETIN" placeholder="ETIN" name ="ETIN" value='{{$row->ETIN}}' readonly>
					</div>
					<div class="form-group col-md-6">
						<label for="status" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Current Product Status, i.e. Active, Deplete, Discontinued, Blocked, Pending">Status <span class="text-danger">*</span></label>
						<select id="status" name="status" class="form-control select2" >
							<option value=""> -- Select a value  -- </option>
							<option value="Active" <?php if($row->status == 'Active' ) echo "selected";?>>Active</option>
							<option value="Deplete" <?php if($row->status == 'Deplete' ) echo "selected";?>>Deplete</option>
							<option value="Discontinued" <?php if($row->status == 'Discontinued' ) echo "selected";?>>Discontinued</option>
							<option value="Blocked" <?php if($row->status == 'Blocked' ) echo "selected";?>>Blocked</option>
							<option value="Pending" <?php if($row->status == 'Pending' ) echo "selected";?>>Pending</option>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="ETIN of how e-tailer purchases the item">Parent ETIN</label>
						<input type="text" class="form-control" id="parent_ETIN" placeholder="Parent ETIN" name ="parent_ETIN" readonly value='{{$row->parent_ETIN}}'>
					</div>
					<div class="form-group col-md-6">
						<label for="etailer_availability" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Indicates whether the item is stocked in house, special order or dropshipped, i.e. Stocked, Special Order, Dropship">e-tailer Availability <span class="text-danger">*</span></label>
						<select id="etailer_availability" name="etailer_availability" class="form-control select2" id="etailer_availability" >
								<option value=''> -- Select a value  -- </option>
								@if ($etailers)
									@foreach($etailers as $etailer)
										<option value="{{ $etailer }}" <?php if($row->etailer_availability == $etailer ) echo "selected";?>>{{ $etailer }}</option>
									@endforeach	
								@endif
							
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="alternate_ETINs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Previous ETINs used">Alternate ETINs</label>
						<input type="text" class="form-control" id="alternate_ETINs" placeholder="Alternate ETINs" name ="alternate_ETINs" value='{{$row->alternate_ETINs}}'>
					</div>
					<div class="form-group col-md-6">
						<label for="product_temperature" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Product temperature category, i.e. Dry-Fragile, Frozen, Refrigerated, Dry-Strong">Product Temperature <span class="text-danger">*</span></label>
						<select id="product_temperature" name="product_temperature" class="form-control select2" id="product_temperature" >
								<option value='' selected> -- Select a value  -- </option>
							@if ($producttemp)
								@foreach($producttemp as $producttemplist)
									<option value="{{ $producttemplist }}" <?php if($row->product_temperature == $producttemplist ) echo "selected";?>>{{ $producttemplist }}</option>
								@endforeach
							@endif
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="product_listing_ETIN" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Listing SKU for 3PL Client Requirements">Product listing ETIN</label>
						<input type="text" class="form-control" id="product_listing_ETIN" name="product_listing_ETIN" placeholder="Product listing ETIN" value='{{$row->product_listing_ETIN}}'>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<label for="warehouses_assigned" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Warehouse(s) stocking this product">Warehouse(s) Assigned <span class="text-danger">*</span></label>
						<table class="table table-bordered">
							<tr>
								<th></th>
								<th>Stocked</th>
								<th>On-Hand</th>
							</tr>
							<?php $warehouses_assigned = explode(',',$row->warehouses_assigned); ?>
							@if ($warehouse)
								@foreach($warehouse as $warehouses)
									<tr>
										<td>{{ $warehouses }}</td>
										<td><input type="checkbox" name="warehouses_assigned[]" value="{{ $warehouses }}" <?php if(in_array($warehouses , $warehouses_assigned)) echo 'checked';?>></td>
										<td></td>
									</tr>
								@endforeach
							@endif
						</table>
						
								
							
					</div>
				</div>
			</div>
			
			<div class="col-md-12">
				<ul class="nav nav-tabs nav-justified">
					<li class="nav-item">
						<a class="nav-link active" href="#tab_product_detail" id="product_detail_tab" role="tab" aria-controls="product_detail_tab" area-selected="true" data-toggle="tab">Kit Configuration & Product Details</a>
					</li>
					<li class="nav-item">
						<a class="nav-link " href="#tab_description" id="description_tab" role="tab" aria-controls="description_tab" area-selected="false" data-toggle="tab">Description & Category</a>
					</li>
					<li class="nav-item">
						<a class="nav-link " href="#tab_supplier" id="supplier_tab" role="tab" aria-controls="supplier_tab" area-selected="false" data-toggle="tab">Supplier</a>
					</li>
					<li class="nav-item">
						<a class="nav-link " href="#tab_clients" id="clients_tab" role="tab" aria-controls="clients_tab" area-selected="false" data-toggle="tab">Clients & Sites</a>
					</li>
					<li class="nav-item">
						<a class="nav-link " href="#tab_manufacturer" id="manufacturer_tab" role="tab" aria-controls="manufacturer_tab" area-selected="false" data-toggle="tab">Manufacturer & Suppliers</a>
					</li>
					<li class="nav-item">
						<a class="nav-link " href="#tab_images" id="images_tab" role="tab" aria-controls="images_tab" area-selected="false" data-toggle="tab">Images</a>
					</li>
					<li class="nav-item">
						<a class="nav-link " href="#tab_misc" id="misc_tab" role="tab" aria-controls="misc_tab" area-selected="false" data-toggle="tab">Misc.</a>
					</li>
					<!-- <li class="nav-item">
						<a class="nav-link " href="#tab_inventory" id="inventory_tab" role="tab" aria-controls="inventory_tab" area-selected="false" data-toggle="tab">Product Inventory</a>
					</li> -->
					<li class="nav-item">
						<a class="nav-link " href="#tab_history" id="history_tab" role="tab" aria-controls="history_tab" area-selected="false" data-toggle="tab">History</a>
					</li>
					
				</ul>

				<div class="tab-content">
					<div class="tab-pane fade show active" id="tab_product_detail" role="tabpanel" area-labelledby="product_detail_tab">
						<div class="row mt-4">
							<div class="col-lg-8">
								<p><b>Select Components</b></p>
								<table class="table table-border table-stripped" id="parent_products">
									<thead>
										<tr>
											<td>ETIN</td>
											<td>Brand</td>
											<td>Product Listing Name</td>
											<td>Pack From Count</td>
											<td>Unit In Pack</td>
											<td>Unit Description</td>
											<td>Unit Size</td>
											<td>UPC</td>
											<td>Action</td>
										</tr>
									</thead>
								</table>
							</div>
							<div class="col-lg-4">
								<p><b>Selected Components</b></p>
								<input type="hidden" id="selected_products" value="{{ $selected_products }}">
                                <input type="hidden" id="unit_in_pack" value="0" name="unit_in_pack">
								<table class="table table-border table-stripped" id="parent_products_selected">
									<thead>
										<tr>
											<td>ETIN</td>
											<td>Product Listing Name</td>
											<td>Qty</td>
											<td>Action</td>
										</tr>
									</thead>
                                    <tbody></tbody>
								</table>
							</div>
						</div>
						<div class="row mt-4">
							<div class="col-md-4">
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="The product's flavor or variety description">Flavor Variation</label>
									<input type="text" class="form-control" id="flavor" name="flavor" placeholder="Flavor Variation" value='{{$row->flavor}}'>
								</div>
								<div class="form-group col-md-12">
									<label for="brand" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Brand name">Brand <span class="text-danger">*</span></label>
									<input type="text" name="brand" id="brand" class="form-control" readonly>
								</div>
								<div class="form-group col-md-12">
									<label for="manufacturer" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Manufacturer name">Manufacturer <span class="text-danger">*</span></label>
                                    <input type="text" name="manufacturer" id="manufacturer" class="form-control" readonly>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Base product name, i.e. Ice Cream, Energy Drink, Potato Chips, etc.">Product Type <span class="text-danger">*</span></label>
									<select id="product_type" name="product_type" class="form-control select2" >
										<option value='' selected> -- Select a value  -- </option>
										@if ($producttype)
											@foreach($producttype as $producttypelist)
												<option value="{{ $producttypelist }}" <?php if($row->product_type == $producttypelist ) echo "selected";?>>{{ $producttypelist }}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="table-responsive card_">
									<table class="table ">
										<thead>
											<tr>
												<td colspan="2"><h3 class="card-title text-center">Product Dimensions Unit</h3></td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Weight  of the item/unit in pounds (lbs.)">
													Weight (lbs) 
												</td>
												<td>
													<input type="number" class="form-control text-center" id="weight" name="weight" placeholder="Weight (lbs)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" value="{{ $row->weight }}">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Length of the item/unit in inches (in)">
													Length (in) 
												</td>
												<td>
													<input type="number" class="form-control text-center" id="length" name="length" placeholder="Length (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  value="{{ $row->length }}">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Width (depth)of the item/unit in inches (in)">
													Width (in) 
												</td>
												<td>
													<input type="number" class="form-control text-center" id="width" name="width" placeholder="Width (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  value="{{ $row->width }}"> 
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Height of the item/unit in inches (in)">
													Height (in) 
												</td>
												<td>
													<input type="number" class="form-control text-center" id="height" name="height" placeholder="Height (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  value="{{ $row->height }}"> 
												</td>
											</tr>			
										</tbody>
									</table>
								</div>
								
								
							</div>
							
							<div class="col-md-4">
								<div class="table-responsive card_">
									<table class="table ">
										<thead>
											<tr>
												<td colspan="2"><h4 class="card-title text-center">Product Flags</h4></td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td scope="row" >
													<label for="prop_65_flag" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Indicates the product contains parts, an ingredient, or is manufactured with chemicals known to cause cancer, birth defects or other reproductive harm defined by the State of California (https://oehha.ca.gov/proposition-65/proposition-65-list)">Prop 65 Flag</label>
												</td>
												<td>
													<div class="form-group col-md-12">
                                                        <input type="text" class="form-control" id="prop_65_flag" name="prop_65_flag" placeholder="Prop 65 Flag" readonly>
													</div>
												</td>
											</tr>
											<tr>
                                                <td scope="row" >
                                                    <label for="prop_65_ingredient"  data-toggle="tooltip" data-placement="top" title="The Prop 65 ingredient(s)/chemical(s)">Prop 65 Ingredient(s) </label>
                                                </td>
												<td >
													<div class="form-group">
														<textarea nput type="text" class="form-control" name="prop_65_ingredient" id="prop_65_ingredient" readonly></textarea>
													</div>
												</td>
											</tr>
											<tr>
												<td scope="row" >
													<label for="hazardous_materials" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Indicates the product is hazardous via Yes/No">Hazardous Materials</label>
												</td>
												<td>
													<input type="text" class="form-control" name="hazardous_materials" id="hazardous_materials" readonly>
													
												</td>
											</tr>

											<tr>
												<td scope="row" >
													<label for="consignment" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Indicates if the item is on consignment">Consignment Product</label>
												</td>
												<td>
													<select class="form-control" id="consignment" name="consignment">
														<option value="">--Select a value--</option>
														<option value="Yes" <?php if($row->consignment == 'Yes') echo "selected";?>>Yes</option>
														<option value="No" <?php if($row->consignment == 'No') echo "selected";?>>No</option>
													</select>
												</td>
											</tr>

											<tr>
												<td scope="row" >
												<label for="POG_flag" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Indicates the product is a planogram item for hospitality clients">POG Flag</label>
												</td>
												<td>
													<select class="form-control" id="POG_flag" name="POG_flag">
														<option value="">--Select a value--</option>
														<option value="Yes" <?php if($row->POG_flag == 'Yes') echo "selected";?>>Yes</option>
														<option value="No" <?php if($row->POG_flag == 'No') echo "selected";?>>No</option>
													</select>
												</td>
											</tr>
													
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade " id="tab_description" role="tabpanel" area-labelledby="description_tab">
						<div class="row mt-4">
							<div class="col-md-4">
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Description of the product to be displayed on stores and marketplaces">Full Product Descrtiption <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="full_product_desc" name="full_product_desc" placeholder="Full Product Descrtiption" value="{{ $row->full_product_desc }}">
								</div>
								<div class="form-group col-md-12">
									<label for="about_this_item" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Bullet points highlighting the item in Amazon's 'About this item' section">About This Item</label>
									<?php $about_this_item = explode('#',$row->about_this_item);?>
									<input type="text" class="form-control  mb-3 @if(ProductEditPermission('about_this_item') == 0) custom_readonly @endif" id="about_this_item_1" name="about_this_item[]" placeholder="Point 1" value="@if(isset($about_this_item[0])){{$about_this_item[0]}}@endif">
									<input type="text" class="form-control  mb-3 @if(ProductEditPermission('about_this_item') == 0) custom_readonly @endif" id="about_this_item_2" name="about_this_item[]" value="@if(isset($about_this_item[1])){{$about_this_item[1]}}@endif" placeholder="Point 2">
									<input type="text" class="form-control  mb-3 @if(ProductEditPermission('about_this_item') == 0) custom_readonly @endif" id="about_this_item_3" name="about_this_item[]" value="@if(isset($about_this_item[2])){{$about_this_item[2]}}@endif" placeholder="Point 3">
									<input type="text" class="form-control  mb-3 @if(ProductEditPermission('about_this_item') == 0) custom_readonly @endif" id="about_this_item_4" name="about_this_item[]" value="@if(isset($about_this_item[3])){{$about_this_item[3]}}@endif" placeholder="Point 4">
									<input type="text" class="form-control mb-3 @if(ProductEditPermission('about_this_item') == 0) custom_readonly @endif" id="about_this_item_5" name="about_this_item[]" value="@if(isset($about_this_item[4])){{$about_this_item[4]}}@endif" placeholder="Point 5">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Top product category of the hierarchy, i.e. Heat & Serve Meals">Product Category <span class="text-danger">*</span></label>
									<input type="hidden" name="product_category" id="product_category">
									<textarea id="product_category_name" class="form-control" readonly></textarea>
								</div>
								<div class="form-group col-md-12">
									<label for="product_subcategory1" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="2nd product category of the hierarchy, i.e. Frozen Meals">Product Sub-Category 1</label>
									<textarea id="product_subcategory1" class="form-control" name="product_subcategory1" readonly></textarea>
								</div>
								<div class="form-group col-md-12">
									<label for="product_subcategory2" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="3rd product category of the hierarchy, i.e. Lunch & Dinner">Product Sub-Category 2</label>
									<textarea id="product_subcategory2" class="form-control" name="product_subcategory2" readonly></textarea>
								</div>
								<div class="form-group col-md-12">
									<label for="product_subcategory3" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="4th product category of the hierarchy, i.e. Pizza">Product Sub-Category 3</label>
									<textarea id="product_subcategory3" class="form-control" name="product_subcategory3" readonly></textarea>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="i.e. Gluten-Free, Vegetarian, Low-fat">Key Product Attributes & Diet</label>
									<textarea type="text" class="form-control" id="key_product_attributes_diet" name="key_product_attributes_diet" placeholder="Key Product Attributes & Diet" readonly></textarea>
								</div>
								
							</div>
							<div class="col-md-4">
                                <label for="lobs" data-toggle="tooltip" data-placement="top" title="Product tags/groups for filtering and identification, i.e. Gluten-Free, Vegetarian, Low-fat, good for you, Hospitality-resort, Hospitality-Urban, etc.">Product Tags Assigned</label>
                                <textarea type="text" name="product_tags" id="product_tags" class="form-control" readonly></textarea>
							</div>
						</div>
					</div>

					<div class="tab-pane fade " id="tab_supplier" role="tabpanel" area-labelledby="supplier_tab">
						<div class="row mt-4">
							<div class="col-md-4">
								
								<div class="table-responsive  col-md-12">
									<table class="table card_">
										<tbody>
											
											
											<tr>
												<td scope="row">Alternate Supplier(s)</td>
												<td>N.A.</td>
											</tr>
											
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Product cost from supplier: (Case Cost / Case Count) x Pack Form">
													Cost <span class="text-danger">*</span>
												</td>
												<td><input type="number" class="form-control" id="cost" name="cost" placeholder="Cost" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$" value="{{ $row->cost }}"></td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="New Product cost from supplier">
													New Cost
												</td>
												<td><input type="number" class="form-control" id="new_cost" name="new_cost" placeholder="New Cost" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$" value="{{ $row->new_cost }}"></td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Date the new cost goes in effect">
													New Cost Date
												</td>
												<td><input type="date" class="form-control" id="new_cost_date" name="new_cost_date" placeholder="New Cost Date" value="{{$row->new_cost_date}}"></td>
											</tr>				
										</tbody>
									</table>
								</div>
							</div>

							<div class="col-md-4">
								<div class="table-responsive  col-md-12 card_">
									<table class="table">
										<thead>
											<tr>
												<td colspan="2"><h3 class="card-title text-center">Product Codes Unit</h3></td>
											</tr>
										</thead>
										<tbody>
											
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Amazon Standard Identification Number">
													ASIN
												</td>
												<td>
													<input type="text" class="form-control text-center" id="asin" name="asin" placeholder="ASIN" value="{{ $row->asin }}">
												</td>
											</tr>
													
										</tbody>
									</table>
								</div>
							</div>

							
						</div>
					</div>

					<div class="tab-pane fade " id="tab_images" role="tabpanel" area-labelledby="images_tab">
						<div class="row mb-2">
							<div class="col-md-12">
								<table class="table table-responsive" style="width: 100%;">
									<thead>
										<tr>
											<th style="width:300px">Image</th>
											<th style="width:300px">Image Text</th>
											<th style="width:300px">Image Type</th>
											<th style="width:300px">Action</th>
										</tr>
									</thead>
									<tbody>
										@if($product_images)
											@foreach($product_images as $image)
												<tr>
													<td><a href="{{ $image->image_url }}" target="_blank"><img src='{{ $image->image_url }}' width="125px" height="75px"></a></td>
													<td>{{$image->image_text}}</td>
													<td>{{$image->image_type}}</td>
													<td><a href="{{route('remove_image',$image->id)}}" onclick="confirm('Are You Sure To Delete Image?')" class="btn btn-danger text-white">Delete</a></td>
												</tr>
											@endforeach
										@endif
									</tbody>
								</table>
							</div>
						</div>
						<div id="about_append">
							<div class="row" id="row1">
								<div class="col-md-4">
								<label for="Image_URL1_Primary" class="ul-form__label">Upload Image <span class="text-danger">*</span></label>
									<input type="file" name="image[1][img]" class="form-control" style="width: 100%;">
								</div>
								<div class="col-md-4">
									<label for="" class="ul-form__label">Product Image Text</label>
									<input type="text" name="image[1][image_text]" class="form-control" style="width: 100%;">
								</div>
								<div class="col-md-3">
									<label for="" class="ul-form__label">Image Type <span class="text-danger">*</span></label>
									<select class="form-control image-type select2" name="image[1][image_type]">
										<option value="">Please Select</option>
										@if($image_types)
											@foreach($image_types as $image_type)
												<option value="{{$image_type->image_type}}">{{$image_type->image_type}}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
						</div>
						<button type="button" class="btn btn-info mt-2" id="add_about" onclick="AddRow()" style="float:right; width:10%">Add Image</button>
					</div>

					<div class="tab-pane fade " id="tab_misc" role="tabpanel" area-labelledby="misc_tab">
						<div class="row">
							<div class="form-group col-md-4">
								<label for="total_ounces" class="ul-form__label">Total Ounces <small>(Auto Generated)</small></label>
								<input type="text" class="form-control" id="total_ounces" name="total_ounces" placeholder="Total Ounces" readonly>
							</div>
						</div>
					</div>

					<div class="tab-pane fade " id="tab_manufacturer" role="tabpanel" area-labelledby="manufacturer_tab">
						<div class="row mt-4">
							<div class="col-md-4">
								
								<div class="form-group col-md-12">
                                    <label for="country_of_origin" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Country where the product is produced, manufactured, or grown">Country of Origin  </label>
                                    <textarea type="text" name="country_of_origin" id="country_of_origin" class="form-control" readonly></textarea>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="List of all ingredients">Ingredients</label>
									<textarea type="text" class="form-control" id="ingredients" name="ingredients" placeholder="Ingredients"readonly></textarea>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Storage Description, i.e. The temperature and humidity ranges are designed to protect the quality attributes of the products. Products should be stored at a temperature of 70F +/- 5F, humidity of 50% +/- 10% Relative Humidity">Storage</label>
									<input type="text" class="form-control"id="storage" name="storage" placeholder="Storage" readonly>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Description of the shipped packaging, i.e. Item is shipped inside of a cooler with dry ice and an outer box">Package Information</label>
									<input type="text" class="form-control" id="package_information" name="package_information" placeholder="Package Information" readonly>
								</div>
							</div>
							<div class="col-md-4">
                               
                                <label for="allergens" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="List of known allergens">Allergens </label>
                                <textarea type="hidden" name="allergens" id="allergens" class="form-control" readonly></textarea>
							</div>
						</div>
					</div>

					

					<div class="tab-pane fade " id="tab_history" role="tabpanel" area-labelledby="history_tab">
						<div class="row">
							<div class="form-group col-md-12">
							<br>
								<h3 class="card-title text-center">Product History</h3>
								<!-- <input type="text" class="form-control text-center" id="duplicate_full_product_desc" name="duplicate_full_product_desc" placeholder="Product History" value="{{ $row->full_product_desc }}"> -->
								<div class="table-responsive">
									<table id="product_history_table" class="table table-bordered mt-3">
										<thead>
											<tr>
												<!-- <th scope="col" id="idclass">#</th> -->
												<th scope="col">Event</th>
												<th scope="col">Description</th>
												<th scope="col">Date</th>
												<th scope="col">User</th>
												<!-- <th scope="col">Action</th> -->
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade " id="tab_clients" role="tabpanel" area-labelledby="clients_tab">
						<div class="row">
							<div class="form-group col-md-6">
                                <label for="lobs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Clients & Sites <span class="text-danger">*</span></label>
                                <textarea  type="hidden" name="lobs" id="lobs" class="form-control" readonly></textarea>
							</div>
							
						</div>
					</div>

					
				</div>
				
			</div>
			<!---------------------------------=================================-------------------------------------->			
		</div>
		<div class="card-footer">
			<div class="mc-footer">
				<div class="row">
					<div class="col-lg-12 text-center">
						<button type="button" id="UpdateRequest" class="btn  btn-primary m-1 submit">Save</button>
						<button type="cancel" class="btn btn-outline-secondary m-1">Cancel</button>
					</div>
				</div>
			</div>
		</div>
	
</div>
</form>
<div id="selected_product_qty" class="modal fade" role="dialog">       
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header-->
			<div class="modal-header" style="background-color:#fff;">
				<h3>Product Quantity</h3>
				<!--Close/Cross Button-->
				<button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
			</div> 
			
			
				<div class="modal-body">
					<label>Qty</label>
					<input type="number" name="qty" class="form-control" id="qty" style="width:100%;" required/> 
                    <input type="hidden" id="pro_id">
				</div> 
				<div class="modal-footer"> 
					<button type="submit" class="btn btn-primary" id="add_pro_qty">Add</button> 
					<a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
				</div>
			
		</div>
	</div>
</div>

@endsection

@section('page-js')
 <script src="{{asset('assets/js/vendor/dropzone.min.js')}}"></script>
<script src="{{asset('assets/js/dropzone.script.js')}}"></script>
<script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script>
$('[data-toggle="tooltip"]').tooltip();
$("button[type = 'submit']").click(function(){
	var $fileUpload = $("input[type='file']");
	if (parseInt($fileUpload.get(0).files.length) > 10){
		alert("You are only allowed to upload a maximum of 10 files");
		return false;
	}
});


// Auto Populate Product Listing Name
$("#brand").on("change", function() {
	inputproductlisting()
});
$("#flavor").on("change", function() {
	inputproductlisting()
});
$("#product_type").on("change", function() {
	inputproductlisting()
});

function inputproductlisting(){
  	var brand = $("#brand").val();
  	var flavor = $("#flavor").val();
  	var item_form_description = $("#item_form_description").val();
	var unit_in_pack = $("#unit_in_pack").val();
	var product_type = $("#product_type").val();
    var productlisting = brand+' '+flavor+' '+ product_type +' ('+ unit_in_pack +' Count Kit)';
    $("#product_listing_name").val('');
    $("#product_listing_name").val(productlisting);
    console.log(productlisting,'Pro');
}


$("#add_pro_qty").click(function(e){
    var pro_id = $("#pro_id").val();
    var qty = $("#qty").val();
    if(qty === ''){
        var ele = "#qty";
        var value = 'Qty can not be empty';
        $(ele).addClass('error_border');
        $('<label class="error">'+ value +'</label>').insertAfter(ele);
        $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
        toastr.error(value);
        return false;
    }

    var combined = pro_id+'#'+qty;
    var selected_products = $("#selected_products").val();
	var selected_products_array = [];
	if(selected_products == ''){
		selected_products_array.push(combined);
		$("#selected_products").val(selected_products_array.join(','));
	}else{
		selected_products_array = selected_products.split(',');
		selected_products_array.push(combined);
		$("#selected_products").val(selected_products_array.join(','));
	}
    $("#pro_id").val('');
    $("#qty").val('');
    $("#qty").removeClass('error_border');
    $('label.error').remove();

	GetParentProducts();
    GetParentSelecedProducts();
    $("#selected_product_qty").modal('hide');
    
})

  
</script>
@endsection

@section('bottom-js')


<script>
	var table = $('#product_history_table').DataTable({
	processing: true,
	ordering: false,
	searching: false,
	serverSide: true,
	destroy: true,	
	autoWidth: false,
	ajax:{
		url: '{{ route('getProductHistory',$row->id) }}',
		method:'GET',
	},
	columns: [
		// {data: 'id', name: 'ID'},
		{data: 'action', name: 'action'},
		{data: 'description', name: 'description'},
		{data: 'created_at', name: 'created_at'},
		{data: 'username', name: 'username'},
		// {data: 'actionbtn', name: 'actionbtn', orderable: false},             
	],
});


$(document).on('change','.all_product_category',function () {
	 var id = $(this).val();
	 var level = $(this).data('id');
	 var myurl = "{{url('getsubcategories')}}" +"/"+ id;
	 var token = "{{csrf_token()}}";
	 
	//  for(var i=level+2; i<=10; i++){
	// 	$('#div_product_subcategory'+i).remove();
	//  }

	 $.ajax({
		url:myurl,
		type:'GET',
		dataType:'json',
		data: {
			'_token': token,
			'_method': 'GET',
			'id' : id,
			},
		success:function (response) {
			var len = 0;
			if (response.data != null) {
				len = response.data.length;
			}
			var html = '';
			$("#div_product_subcategory"+(level+1)).remove();
			if (len>0) {
				html += '<div class="form-group col-md-12" id="div_product_subcategory'+(level+1)+'">';
				html += '<label for="product_subcategory1" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="2nd product category of the hierarchy, i.e. Frozen Meals">Product Sub-Category '+(level+1)+'</label>'
				html += '<select id="product_subcategory'+(level+1)+'" name="product_subcategory'+(level+1)+'" class="form-control select2 all_product_category" id="product_subcategory'+(level+1)+'" data-id="'+(level+1)+'">';
				html += '<option value=""> -- Select a value  -- </option>';
				for (var i = 0; i<len; i++) {
					 var id = response.data[i].id;
					 var name = response.data[i].name;
					 if(name != null )
					{
						html += '<option value='+id+'>'+name+'</option>';
					}
				}
				html += '</select>';
				html += '</div><div id="next_product_container_'+(level+1)+'"></div>';
				$('#next_product_container_'+level).html(html);
				$("#product_subcategory"+(level+1)).select2();
				
			}
		}
	})
});

	$("#product_add").validate({
		submitHandler(form){
			$(".submit").attr("disabled", true);
			var form_cust = $('#product_add')[0]; 
			let form1 = new FormData(form_cust);
			$.ajax({
				type: "POST",
				url: '{{route('kits.ApproveKitRequest',$row->id)}}',
				data: form1, 
				processData: false,
				contentType: false,
				success: function( response ) {
					if(response.error == 0){
						toastr.success(response.msg);
						window.location.href = response.url
					}else{
						$(".submit").attr("disabled", false);
						toastr.error(response.msg);
					}
				},
				error: function(data){
					$(".submit").attr("disabled", false);
					var errors = data.responseJSON;
					$("#error_container").html('');
					$.each( errors.errors, function( key, value ) {
						var ele = "#"+key;
						$(ele).addClass('error_border');
						$('<label class="error">'+ value +'</label>').insertAfter(ele);
						$("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
						toastr.error(value);
					});
				}
			})
			return false;
		}
	});

	$("#UpdateRequest").on('click',function(e){
		$(".submit").attr("disabled", true);
		var form_cust = $('#product_add')[0]; 
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{route('kits.update_request',$row->id)}}',
			data: form1, 
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					location.reload();
				}else{
					$(".submit").attr("disabled", false);
					toastr.error(response.msg);
				}
			},
			error: function(data){
				$(".submit").attr("disabled", false);
				var errors = data.responseJSON;
				$("#error_container").html('');
				$.each( errors.errors, function( key, value ) {
					var ele = "#"+key;
					$(ele).addClass('error_border');
					$('<label class="error">'+ value +'</label>').insertAfter(ele);
					$("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
					toastr.error(value);
				});
			}
		})
	})


</script>

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript">
  $(function () {
	GetParentProducts();
	GetParentSelecedProducts();
  });

  function GetParentProducts(){parent_products
	var table = $('#parent_products').DataTable({
		destroy:true,
        processing: true,
		ordering: false,
	    serverSide: true,
        ajax: {
			url: "{{ route('GetAllParentApprovedProducts') }}",
			method:'GET',
			data: function(d){
				d.ids = $("#selected_products").val();
			}
		},
        columns: [
            {data: 'ETIN', name: 'ETIN'},
			{data: 'brand', name: 'brand'},
			{data: 'product_listing_name', name: 'product_listing_name'},
			{data: 'pack_form_count', name: 'pack_form_count'},
			{data: 'unit_in_pack', name: 'unit_in_pack'},
			{data: 'unit_description', name: 'unit_description'},
			{data: 'unit_size', name: 'unit_size'},
			{data: 'upc', name: 'upc'},
			{data: 'action', name: 'Action', orderable: false},                 
        ],
    });  

  }

  function GetParentSelecedProducts(){
    $("#parent_products_selected tbody").html('');
    $("#brand").val('');
    $("#manufacturer").val('');
    $("#prop_65_flag").val('');
    $("#prop_65_ingredient").val('');
    $("#product_tags").val('');
    $("#lobs").val('');
    $("#country_of_origin").val('');
    $("#allergens").val('');
    $("#unit_in_pack").val('');
	$("#parent_ETIN").val('');
	$("#product_category_name").val('');
	$("#product_category").val('');
	$("#product_subcategory1").val('');
	$("#product_subcategory2").val('');
	$("#product_subcategory3").val('');
	$("#key_product_attributes_diet").val('');
	$("#hazardous_materials").val('');
	$("#storage").val('');
	$("#ingredients").val('');
	$("#package_information").val('');
    $.ajax({
        url: '{{route('GetSelectedProductForKit')}}',
        method:'POST',
        data:{
            ids: $("#selected_products").val() 
        },
        dataType:'JSON',
        success:function(data){
           
            $("#parent_products_selected tbody").html(data.table_data);
            $("#brand").val(data.brand);
            $("#manufacturer").val(data.manufacturer);
            $("#prop_65_flag").val(data.prop_65_flag);
            $("#prop_65_ingredient").val(data.prop_65_ingredient);
            $("#product_tags").val(data.product_tags);
            $("#lobs").val(data.lobs);
            $("#country_of_origin").val(data.country_of_origin);
            $("#allergens").val(data.allergens);
            $("#unit_in_pack").val(data.unit_in_pack);
			$("#parent_ETIN").val(data.parent_ETIN);
			$("#product_category_name").val(data.product_category_name);
			$("#product_category").val(data.product_category);
			$("#product_subcategory1").val(data.product_subcategory1);
			$("#product_subcategory2").val(data.product_subcategory2);
			$("#product_subcategory3").val(data.product_subcategory3);
			$("#key_product_attributes_diet").val(data.key_product_attributes_diet);
			$("#hazardous_materials").val(data.hazardous_materials);
			$("#storage").val(data.storage);
			$("#ingredients").val(data.ingredients);
			$("#package_information").val(data.package_information);
			
			inputproductlisting();
        }
    });
  }


  function selectProduct(id){
    $("#selected_product_qty").modal('show');
    $("#pro_id").val(id);
  }

  function removeProduct(id){
    var removed_products = id;
    var products = $("#selected_products").val();
    var all_pro = [];
    if(products == ''){
        
    }else{
        all_pro = products.split(',');
        all_pro.splice($.inArray(removed_products, all_pro), 1);
        $("#selected_products").val(all_pro.join(','));
    }
    GetParentProducts();
    GetParentSelecedProducts();
  }

var i = 1;
function AddRow(){
	i++;
	var html = '';
	html+='<div class="row mt-2 mb-2" id="row'+i+'">';
	html+='<div class="col-md-4">';
	html+='<label for="Image_URL1_Primary" class="ul-form__label">Upload Image <span class="text-danger">*</span></label><input type="file" name="image['+i+'][img]"  id="image'+i+'" class="form-control" style="width: 100%;"></div>';
	html+='<div class="col-md-4"><label for="" class="ul-form__label">Product Image Text</label><input type="text" name="image['+i+'][image_text]"  id="image_text'+i+'" class="form-control" style="width: 100%;"></div>';
	html+='<div class="col-md-3"><label for="" class="ul-form__label">Image Type <span class="text-danger">*</span></label><select class="form-control" name="image['+i+'][image_type]" id="image_type'+i+'"><option value="">Please Select</option>@if($image_types)@foreach($image_types as $image_type)<option value="{{$image_type->image_type}}">{{$image_type->image_type}}</option>@endforeach @endif</select></div>';
	html+='<div class="col-md-1"><button type="button" class="btn btn-danger" id="remove_about" onclick="RemoveRow('+i+')" style="margin-top:40px;"><i class="far fa-window-close"></i></button></div></div>';
	console.log(html);
	$("#about_append").append(html);  

	$("#image_type"+i).select2(); 
	
}

function RemoveRow(id){
	$("#row"+id).remove();
}


</script>

@endsection