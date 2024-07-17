@extends('layouts.master')
@section('before-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/dropzone.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/custom/css/custom.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">


<style>

.select2-container--default .select2-selection--multiple{
	padding: 0 !important;
	letter-spacing: 0 !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered li{
	width: 100px !important;
}

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


<div class="card">
	<div class="card-header bg-transparent">
		<div class="row">
			<div class="col-md-6">
				<h3 class="card-title"> Edit Kit Product</h3>
			</div>
			<div class="col-md-6">


			</div>
		</div>
		<!-- @if($row->is_approve == 0)
			<a href="#" id="ApproveKit"><div direction="left" class="marqueeclass">This Product is not approved yet. Click Here  to approve </div></a>
		@endif
		@if($row->is_approve == 1)
			<div direction="left" class="sucessmarqueeclass">This Product is LIVE. </div>
		@endif -->
		<div id="error_container"></div>
	</div>

<!--begin::form-->
	<form method="POST" action="#" enctype="multipart/form-data" id="product_add">
	@method('put')
	@csrf
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
							@if($product_status)
								@foreach($product_status as $row_status)
									<option value="{{$row_status->product_status}}" <?php if($row->status == $row_status->product_status ) echo "selected";?>>{{$row_status->product_status}}</option>
								@endforeach
							@endif
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
									@foreach($etailers as $key=>$etailer)
										<option value="{{ $key }}" <?php if($row->etailer_availability == $key ) echo "selected";?>>{{ $etailer }}</option>
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
										<td>{{$onHandQty[$warehouses]}}</td>
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
						<a class="nav-link " href="#tab_manufacturer" id="manufacturer_tab" role="tab" aria-controls="manufacturer_tab" area-selected="false" data-toggle="tab">Manufacturer</a>
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
							<div class="col-lg-7">
							<input type="hidden" id="search_dt">
								<p><b>Select Components</b></p>
								<table class="table table-border table-stripped" id="parent_products">
									<thead>
										<tr>
											<td>ETIN</td>
											<td>Brand</td>
											<td>Product Listing Name</td>
											<!-- <td>Pack From Count</td> -->
											<!-- <td>Unit In Pack</td>
											<td>Unit Description</td>
											<td>Unit Size</td> -->
											<td>UPC</td>
											<td>Action</td>
										</tr>
									</thead>
									<!-- <thead>
										<tr>
											<td>
												<select name="etin_filter[]" id="etin_filter" class="form-control select2 " multiple>
													<option value="">Select</option>

													@foreach ($getet as $row_etin)
														<option value="{{ $row_etin }}">{{ $row_etin }}</option>
													@endforeach
												</select>
											</td>
											<td>
												<select  id="brand_filter" name="brand_filter[]" class="form-control select2" multiple>
													<option value=''>Select</option>
													@foreach($brand as $brandlist)
														<option value="{{ $brandlist }}">{{ $brandlist }}</option>
													@endforeach
												</select>
											</td>
											<td>
												<select id="product_list_filter" name="product_list_filter[]" class="form-control select2" multiple>
													<option value=''>Select</option>
													@foreach($product_listing_name as $product_list)
														<option value="{{ $product_list }}">{{ $product_list }}</option>
													@endforeach
												</select>
											</td>
											<td>
												<select id="upc_filter" name="upc_filter[]" class="form-control select2" multiple>
													<option value=''>Select</option>
													@foreach($upcs as $upc)
														<option value="{{ $upc }}">{{ $upc }}</option>
													@endforeach
												</select>
											</td>
											<td></td>
										</tr>
									</thead> -->
								</table>
							</div>
							<div class="col-lg-5">
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

								<div class="form-group col-md-12">
									<label for="cost" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Product cost from supplier: (Case Cost / Case Count) x Pack Form">Cost <span class="text-danger">*</span></label>
									<input type="number" class="form-control" id="cost" name="cost" placeholder="Cost" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$" value="{{ $row->cost }}">
								</div>

                                <div class="form-group col-md-12">
									<label for="acquisition_cost" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Acquisition Cost">Acquisition Cost</label>
									<input type="number" class="form-control" id="acquisition_cost" name="acquisition_cost" placeholder="Acquisition Cost" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$" value="{{ $row->acquisition_cost }}">
								</div>

								<div class="form-group col-md-12">
									<label for="new_cost" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="New Product cost from supplier">New Cost</label>
									<input type="number" class="form-control" id="new_cost" name="new_cost" placeholder="New Cost" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$" value="{{ $row->new_cost }}">
								</div>

								<div class="form-group col-md-12">
									<label for="new_cost_date" class="ul-form__label" data-toggle="tooltip" data-placement="top"title="Date the new cost goes in effect">New Cost Date </label>
									<input type="date" class="form-control" id="new_cost_date" name="new_cost_date" placeholder="New Cost Date" value="{{$row->new_cost_date}}">
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
														<textarea nput type="text" class="form-control" name="prop_65_ingredient_names" id="prop_65_ingredient_names" readonly></textarea>
														<input type="hidden" class="form-control" name="prop_65_ingredient" id="prop_65_ingredient" readonly>
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

								<div class="col-md-12">
									<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Top product category of the hierarchy, i.e. Heat & Serve Meals">Product Category <span class="text-danger">*</span></label>
									<select id="product_category" name="product_category" class="form-control select2 all_product_category <?php if(isset($producthistory)){ if($producthistory->product_category != $productdetails->product_category) echo "edited-field";}?> @if(ProductEditPermission('product_category') == 0) custom_readonly @endif" id="product_category"  data-id='0'>
										<option value=''> -- Select a value  -- </option>
										@if ($categories)
											@foreach($categories as $row_cat)
												<option value="{{ $row_cat->id }}" <?php if($row->product_category == $row_cat->id ) echo "selected";?>>{{ $row_cat->name }}</option>
											@endforeach
										@endif
									</select>
								</div>


								<div id="next_product_container_0">
									@for($i=1; $i<=9;$i++)
										<?php $name = 'product_subcategory'.$i;?>
										@if($row->$name != '')
											<div class="form-group col-md-12">
												<label for="{{$name}}" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="2nd product category of the hierarchy, i.e. Frozen Meals">Product Sub-Category {{$i}}</label>
												<select id="{{$row->$name}}" name="{{$row->$name}}" class="form-control select2 <?php if(isset($producthistory)){ if($producthistory->$name != $row->$name) echo "edited-field";}?> @if(ProductEditPermission($name) == 0) custom_readonly @endif" id="{{$name}}" >
													<option value="">Select</option>
													<option value='{{$row->$name}}' selected> {{CategoryName($row->$name)}} </option>
												</select>
											</div>
											<div id="next_product_container_{{$i}}"></div>
										@endif
									@endfor
								</div>
								</div>

								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="i.e. Gluten-Free, Vegetarian, Low-fat">Key Product Attributes & Diet</label>
									<textarea type="text" class="form-control" id="key_product_attributes_diet" name="key_product_attributes_diet" placeholder="Key Product Attributes & Diet" readonly></textarea>
								</div>

							</div>

							<div class="col-md-4">
							<!-- <div class="form-group col-md-12">
								<label for="product_tags"  data-toggle="tooltip" data-placement="top" title="Product tags/groups for filtering and identification, i.e. Gluten-Free, Vegetarian, Low-fat, good for you, Hospitality-resort, Hospitality-Urban, etc.">Product Tags</label>
								<textarea id="product_tags" class="form-control" name="product_tags" readonly></textarea>
							</div> -->
								<div class="form-group col-md-6">
									<label for="product_tags"  data-toggle="tooltip" data-placement="top" title="Product tags/groups for filtering and identification, i.e. Gluten-Free, Vegetarian, Low-fat, good for you, Hospitality-resort, Hospitality-Urban, etc.">Product Tags Not Assigned</label>
									<div class="custom_one_line_cards_container ProductTagsDrop border">
										<?php $product_tags = explode(',',$row->product_tags); ?>
										@if ($producttag)
											@foreach($producttag as $key=>$producttaglist)
												@if(!in_array($key,$product_tags))
													<div class="product_tags_cards custom_one_line_cards" id="{{ $key }}">{{ $producttaglist }}</div>
												@endif
											@endforeach
										@endif
									</div>
								</div>
								<div class="form-group col-md-6">
									<input type="hidden" name="product_tags" id="product_tags" value="{{$row->product_tags}}">
									<label for="lobs"  data-toggle="tooltip" data-placement="top" title="Product tags/groups for filtering and identification, i.e. Gluten-Free, Vegetarian, Low-fat, good for you, Hospitality-resort, Hospitality-Urban, etc.">Product Tags Assigned</label>
									<div class="custom_one_line_cards_container ProductTagsDropAssigned border">
										@if ($producttag)
											@foreach($producttag as $key=>$producttaglist)
												@if(in_array($key,$product_tags))
													<div class="product_tags_cards custom_one_line_cards" id="{{ $key }}">{{ $producttaglist }}</div>
												@endif
											@endforeach
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="tab-pane fade " id="tab_supplier" role="tabpanel" area-labelledby="supplier_tab">
						<div class="row mt-4">
							<div class="col-md-4">
								<div class="table-responsive  col-md-12 card_">
									<table class="table">
										<thead>
											<tr>
												<td colspan="2"><h3 class="card-title text-center">Product Codes Case</h3></td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Supplier's product number used for purchasing">
													Supplier Product Number
												</td>
												<td>
													<input type="text" class="form-control text-center" placeholder="Supplier Product Number" id="supplier_product_number" name="supplier_product_number" value="{{ $row->supplier_product_number }}">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Manufacturer's product number">
													Manufacturer Product Number
												</td>
												<td>
													<input type="text" class="form-control text-center" placeholder="Manufacturer Product Number" id="manufacture_product_number" name="manufacture_product_number" value="{{ $row->manufacture_product_number }}">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="12 Digit UPC (Universal Product Code) of the item/unit being sold">
													UPC
													<!-- <span class="text-danger">*</span> -->
												</td>
												<td>
													<input type="checkbox" id="upc_present" name="upc_present" onchange="disableEnableTextBox(this, 'upc')" checked=true>
													<input type="text" class="form-control text-center" id="upc" name="upc" placeholder="UPC" minlength="12" maxlength="12" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='validatetext(event)' value="{{ $row->upc }}">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="UPC Scanable">UPC Scanable</td>
												<td>
													<input type="checkbox" id="upc_scanable" name="upc_scanable" <?php if ($row->upc_scanable == "1") { echo 'checked'; }?> />
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="14 Digit case GTIN (Global Trade Identification Number) of the item/unit being sold">
													GTIN
													<!-- <span class="text-danger">*</span> -->
												</td>
												<td>
													<input type="checkbox" id="gtin_present" name="gtin_present" onchange="disableEnableTextBox(this, 'gtin')" checked=true>
													<input type="text" class="form-control text-center" id="gtin" name="gtin" placeholder="GTIN" minlength="14" maxlength="14" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='validatetext(event)' value="{{ $row->gtin }}">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="GTIN Scanable">GTIN Scanable</td>
												<td>
													<input type="checkbox" id="gtin_scanable" name="gtin_scanable" <?php if ($row->gtin_scanable == "1") { echo 'checked'; }?> />
												</td>
											</tr>
											
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Amazon Standard Identification Number">
													ASIN
												</td>
												<td>
													<input type="text" class="form-control text-center" id="asin" name="asin" placeholder="ASIN" value="{{ $row->asin }}">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Global Product Classification Code for international shipping etc.">
													GPC Code
												</td>
												<td>
													<input type="text" class="form-control text-center" id="GPC_code" name="GPC_code" placeholder="GPC Code" value="{{ $row->GPC_code }}">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Global Product Classification Category for international shipping etc.">
													GPC Class
												</td>
												<td>
													<input type="text" class="form-control text-center" id="GPC_class" name="GPC_class" placeholder="GPC Class" value="{{ $row->GPC_class }}">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Harmonized System for International shipping">
													HS Code
												</td>
												<td>
													<input type="text" class="form-control text-center" id="HS_code" name="HS_code" placeholder="HS Code" value="{{ $row->HS_code }}">
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
													<td>
														<a href="#" onClick="GetModel('{{route('imagetext',$image->id)}}')" class="btn btn-info text-white">Edit</a>
														<a href="{{route('remove_image',$image->id)}}" onclick="confirm('Are You Sure To Delete Image?')" class="btn btn-danger text-white">Delete</a>
													</td>
												</tr>
											@endforeach
										@endif
									</tbody>
								</table>
							</div>
						</div>
						<div class="modal fade" id="MyModal" data-backdrop="static">
						</div>
						<div id="about_append">
							<div class="row" id="row1">
								<div class="col-md-4">
								<label for="Image_URL1_Primary" class="ul-form__label">Upload Image</label>
									<input type="file" name="image[1][img]" class="form-control" style="width: 100%;">
								</div>
								<div class="col-md-4">
									<label for="" class="ul-form__label">Product Image Text</label>
									<input type="text" name="image[1][image_text]" class="form-control" style="width: 100%;">
								</div>
								<div class="col-md-3">
									<label for="" class="ul-form__label">Image Type</label>
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
                                    <textarea type="text" name="country_of_origin_names" id="country_of_origin_names" class="form-control" readonly></textarea>
									<input type="hidden" name="country_of_origin" id="country_of_origin" class="form-control" readonly>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="List of all ingredients">Ingredients</label>
									<textarea type="text" class="form-control" id="ingredients" name="ingredients" placeholder="Ingredients" value="{{$row->ingredients}}" readonly></textarea>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Storage Description, i.e. The temperature and humidity ranges are designed to protect the quality attributes of the products. Products should be stored at a temperature of 70F +/- 5F, humidity of 50% +/- 10% Relative Humidity">Storage</label>
									<input type="text" class="form-control"id="storage" name="storage" placeholder="Storage" value="{{$row->storage}}" readonly>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Description of the shipped packaging, i.e. Item is shipped inside of a cooler with dry ice and an outer box">Package Information</label>
									<input type="text" class="form-control" id="package_information" name="package_information" placeholder="Package Information" value="{{$row->package_information}}" readonly>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group col-md-12">
									<label for="allergens" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Known allergens">Allergens</label>
									<input type="text" class="form-control" id="allergens_names" name="allergens_names" placeholder="Allergense" readonly>
									<input type="hidden" class="form-control" id="allergens" name="allergens" placeholder="Allergense" readonly>
									<!-- <input type="text" class="form-control" id="allergens" name="allergens" placeholder="Allergense" value="{{$row->allergens}}" readonly> -->
								</div>
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
								<!-- <div class="form-group col-md-12">
									<label for="lobs"  data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Clients & Sites</label>
									<textarea id="lobs" class="form-control" name="lobs" readonly></textarea>
								</div> -->
								 <div class="form-group col-md-6">
									<label for="lobs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Clients & Sites Not Assigned</label>
									<div class="custom_one_line_cards_container LobsDrop  border">
										<?php  $lobs = explode(',',$row->lobs); ?>
										@if ($client)
											@foreach($client as $key=>$clients)
												@if(!in_array($key,$lobs))
													<div class="lobs_cards custom_one_line_cards" id="{{ $key }}">{{ $clients }}</div>
												@endif
											@endforeach
										@endif
									</div>
								</div>
								<div class="form-group col-md-6">
									<input type="hidden" name="lobs" id="lobs" value="{{ $row->lobs }}">
									<label for="lobs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Clients & Sites Assigned <span class="text-danger">*</span></label>
									<div class="custom_one_line_cards_container LobsDropAssigned border">
										@if ($client)
											@foreach($client as $key=>$clients)
												@if(in_array($key,$lobs))
													<div class="lobs_cards custom_one_line_cards" id="{{ $key }}">{{ $clients }}</div>
												@endif
											@endforeach
										@endif
									</div>
								</div>
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
						<button type="submit" class="btn  btn-primary m-1 submit">Save</button>
						<button type="cancel" class="btn btn-outline-secondary m-1">Cancel</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

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
	$('#ingredients').val("{{$row->ingredients}}");
	$('[data-toggle="tooltip"]').tooltip();
	$("button[type = 'submit']").click(function(){
		var $fileUpload = $("input[type='file']");
		if (parseInt($fileUpload.get(0).files.length) > 10){
			alert("You are only allowed to upload a maximum of 10 files");
			return false;
		}
	});


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

	$('.ProductTagsDrop ').on('click','.product_tags_cards',function(e){
		var dropped_product_tags = $(this).attr('id');
		console.log(dropped_product_tags);
		var product_tags_assigned = $("#product_tags").val();
		var product_tags_array = [];
		if(product_tags_assigned == ''){
			product_tags_array.push(dropped_product_tags);
			$("#product_tags").val(product_tags_array.join(','));
		}else{
			product_tags_array = product_tags_assigned.split(',');
			product_tags_array.push(dropped_product_tags);
			$("#product_tags").val(product_tags_array.join(','));
		}

		$('.ProductTagsDropAssigned').append(this);
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

	$(".ProductTagsDropAssigned").on('click','.product_tags_cards',function(e){
		var dropped_product_tags = $(this).attr('id');
		console.log(dropped_product_tags);
		var product_tags_assigned = $("#product_tags").val();
		var product_tags_array = [];
		if(product_tags_assigned == ''){

		}else{
			product_tags_array = product_tags_assigned.split(',');
			product_tags_array.splice($.inArray(dropped_product_tags, product_tags_array), 1);
			$("#product_tags").val(product_tags_array.join(','));
		}
		$('.ProductTagsDrop').append(this);
	});

	$('.AllergensDrop ').on('click','.allergens_cards',function(e){
		var dropped_allergens = $(this).attr('id');
		console.log(dropped_allergens);
		var allergens_assigned = $("#allergens").val();
		var allergens_array = [];
		if(allergens_assigned == ''){
			allergens_array.push(dropped_allergens);
			$("#allergens").val(allergens_array.join(','));
		}else{
			allergens_array = allergens_assigned.split(',');
			allergens_array.push(dropped_allergens);
			$("#allergens").val(allergens_array.join(','));
		}

		$('.AllergensDropAssigned').append(this);
	});

	$(".AllergensDropAssigned").on('click','.allergens_cards',function(e){
		var dropped_allergens = $(this).attr('id');
		console.log(dropped_allergens);
		var allergens_assigned = $("#allergens").val();
		var allergens_array = [];
		if(allergens_assigned == ''){

		}else{
			allergens_array = allergens_assigned.split(',');
			allergens_array.splice($.inArray(dropped_allergens, allergens_array), 1);
			$("#allergens").val(allergens_array.join(','));
		}
		$('.AllergensDrop').append(this);
	});

	$('.LobsDrop ').on('click','.lobs_cards',function(e){
		var dropped_lobs = $(this).attr('id');
		console.log(dropped_lobs);
		var lobs_assigned = $("#lobs").val();
		var lobs_array = [];
		if(lobs_assigned == ''){
			lobs_array.push(dropped_lobs);
			$("#lobs").val(lobs_array.join(','));
		}else{
			lobs_array = lobs_assigned.split(',');
			lobs_array.push(dropped_lobs);
			$("#lobs").val(lobs_array.join(','));
		}

		$('.LobsDropAssigned').append(this);
	});

	$(".LobsDropAssigned").on('click','.lobs_cards',function(e){
		var dropped_lobs = $(this).attr('id');
		console.log(dropped_lobs);
		var lobs_assigned = $("#lobs").val();
		var lobs_array = [];
		if(lobs_assigned == ''){

		}else{
			lobs_array = lobs_assigned.split(',');
			lobs_array.splice($.inArray(dropped_lobs, lobs_array), 1);
			$("#lobs").val(lobs_array.join(','));
		}
		$('.LobsDrop').append(this);
	});


</script>
@endsection

@section('bottom-js')


<script>
	$("#product_add").validate({
		submitHandler(form){
			$(".submit").attr("disabled", true);
			var form_cust = $('#product_add')[0];
			let form1 = new FormData(form_cust);
			$.ajax({
				type: "POST",
				url: '{{route('kits.update',$row->id)}}',
				data: form1,
				processData: false,
				contentType: false,
				success: function( response ) {
					if(response.error == 0){
						toastr.success(response.msg);
						// window.location.href = response.url
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
			return false;
		}
	});


</script>

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script type="text/javascript">
  $(function () {
	GetParentProducts();
	GetParentSelecedProducts();
  });

 	$("#etin_filter").on('change',function(){
        GetParentProducts();
    });
	$("#brand_filter").on('change',function(){
        GetParentProducts();
    });
	$("#product_list_filter").on('change',function(){
        GetParentProducts();
    });
	$("#upc_filter").on('change',function(){
        GetParentProducts();
    });

  function GetParentProducts(){
	var table = $('#parent_products').DataTable({
		destroy:true,
        processing: true,
		ordering: false,
	    serverSide: true,
		search: {
			"search": $('#search_dt').val()
		},
        ajax: {
			url: "{{ route('GetAllParentApprovedProducts') }}",
			method:'GET',
			data: function(d){
				d.ids = $("#selected_products").val();
				d.etin_filter = $("#etin_filter").val();
				d.brand_filter = $("#brand_filter").val();
				d.product_list_filter = $("#product_list_filter").val();
				d.upc_filter = $("#upc_filter").val();
			}
		},
        columns: [
            {data: 'ETIN', name: 'ETIN'},
			{data: 'brand', name: 'brand'},
			{data: 'product_listing_name', name: 'product_listing_name'},
			// {data: 'pack_form_count', name: 'pack_form_count'},
			// {data: 'unit_in_pack', name: 'unit_in_pack'},
			// {data: 'unit_description', name: 'unit_description'},
			// {data: 'unit_size', name: 'unit_size'},
			{data: 'upc', name: 'upc'},
			{data: 'action', name: 'Action', orderable: false},
        ],
		oLanguage: {
                "sSearch": "Search:",

		}
    });

  }

  	$('#parent_products').on('search.dt', function() {
    	var value = $('.dataTables_filter input').val();
    	$('#search_dt').val(value);
	});

	function GetParentSelecedProducts(){
		$("#parent_products_selected tbody").html('');
		$("#brand").val('');
		$("#manufacturer").val('');
		$("#prop_65_flag").val('');
		$("#prop_65_ingredient").val('');
		$("#prop_65_ingredient_names").val('');
		$("#product_tags").val('');
		$("#lobs").val('');
		$("#country_of_origin").val('');
		$("#country_of_origin_names").val('');
		$("#allergens").val('');
		$("#allergens_names").val('');
		$("#unit_in_pack").val('');
		$("#parent_ETIN").val('');
		// $("#product_category_name").val('');
		// $("#product_category").val('');
		// $("#product_subcategory1").val('');
		// $("#product_subcategory2").val('');
		// $("#product_subcategory3").val('');
		$("#key_product_attributes_diet").val('');
		$("#hazardous_materials").val('');
		$("#storage").val('');
		$("#ingredients").val('');
		$("#package_information").val('');
		$('.ProductTagsDropAssigned').empty();
		$('.LobsDropAssigned').empty();
		$("#product_tags").val("");
		$.ajax({
			url: '{{route('GetSelectedProductForKit')}}',
			method:'POST',
			data:{
				ids: $("#selected_products").val(),
				id: '{{$row->id}}'
			},
			dataType:'JSON',
			success:function(data){
				$("#parent_products_selected tbody").html(data.table_data);
				$("#brand").val(data.brand);
				$("#manufacturer").val(data.manufacturer);
				$("#prop_65_flag").val(data.prop_65_flag);
				$("#prop_65_ingredient").val(data.prop_65_ingredient);
				$("#prop_65_ingredient_names").val(data.prop_65_ingredient_names);
				$("#product_tags").val(data.product_tags);
				$("#lobs").val(data.lobs);
				$("#country_of_origin").val(data.country_of_origin);
				$("#country_of_origin_names").val(data.country_of_origin_names);
				$("#allergens").val(data.allergens);
				$("#allergens_names").val(data.allergens_names);
				$("#unit_in_pack").val(data.unit_in_pack);
				$("#parent_ETIN").val(data.parent_ETIN);
				// $("#product_category_name").val(data.product_category_name);
				// $("#product_category").val(data.product_category);
				// $("#product_category").val(data.product_category.split(',')[0]);
				// $('#product_category').select2().trigger('change');
				$("#key_product_attributes_diet").val(data.key_product_attributes_diet);
				$("#hazardous_materials").val(data.hazardous_materials);
				$("#storage").val(data.storage);
				$("#ingredients").val(data.ingredients);
				$("#package_information").val(data.package_information);
				$("#product_tags").val(data.product_tags);
				var tags = data.product_tags_names;
				$.each( tags, function( key, value ) {
					var id = value.replace(' ','_');
					$('.ProductTagsDrop #'+key).remove();
					$('.ProductTagsDropAssigned').append('<div class="product_tags_cards custom_one_line_cards" id='+ key +'>'+value+'</div>');
				});

				var lobs = data.lobs_names;
				console.log(lobs);
				$.each( lobs, function( key, value ) {
					$('.LobsDrop #'+key).remove();
					$('.LobsDropAssigned').append('<div class="lobs_cards custom_one_line_cards" id='+key+'>'+value+'</div>');
				});
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
	html+='<label for="Image_URL1_Primary" class="ul-form__label">Upload Image</label><input type="file" name="image['+i+'][img]"  id="image'+i+'" class="form-control" style="width: 100%;"></div>';
	html+='<div class="col-md-4"><label for="" class="ul-form__label">Product Image Text</label><input type="text" name="image['+i+'][image_text]"  id="image_text'+i+'" class="form-control" style="width: 100%;"></div>';
	html+='<div class="col-md-3"><label for="" class="ul-form__label">Image Type</label><select class="form-control" name="image['+i+'][image_type]" id="image_type'+i+'"><option value="">Please Select</option>@if($image_types)@foreach($image_types as $image_type)<option value="{{$image_type->image_type}}">{{$image_type->image_type}}</option>@endforeach @endif</select></div>';
	html+='<div class="col-md-1"><button type="button" class="btn btn-danger" id="remove_about" onclick="RemoveRow('+i+')" style="margin-top:40px;"><i class="far fa-window-close"></i></button></div></div>';
	console.log(html);
	$("#about_append").append(html);

	$("#image_type"+i).select2();

}

function RemoveRow(id){
	$("#row"+id).remove();
}

// $("#ApproveKit").click(function(){
// 	 swal({
// 		title: 'Do you want to Publish this product1?',
// 		text: "This product will be live",
// 		type: 'warning',
// 		showCancelButton: true,
// 		confirmButtonColor: '#0CC27E',
// 		cancelButtonColor: '#FF586B',
// 		confirmButtonText: 'Yes, Publish it!',
// 		cancelButtonText: 'No, I need to edit this First!',
// 		confirmButtonClass: 'btn btn-success mr-5',
// 		cancelButtonClass: 'btn btn-danger',
// 		buttonsStyling: false
// 	}).then(function () {
// 		if (confirm) {
// 		   $.ajax({
// 			type: "GET",
// 			url: "{{route('ApproveKit', $row->id)}}",
// 			success: function (data) {
// 					location.reload();
// 				}
// 			});
// 		swal(
// 				'Yoooo !',
// 				'Product Published Sussfully!!',
// 				'success'
// 			)
// 		}

// 	}, function (dismiss) {
// 		// dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
// 		if (dismiss === 'cancel') {
// 			swal({
// 				title: 'Product not Published!!',
// 				text: "Reviewing Product.",
// 				type: 'warning',
// 				showCancelButton: true,
// 				confirmButtonColor: '#0CC27E',
// 				cancelButtonColor: '#FF586B',
// 				confirmButtonText: 'Delete this permanently!',
// 				cancelButtonText: 'Edit This Product.',
// 				confirmButtonClass: 'btn btn-success mr-5',
// 				cancelButtonClass: 'btn btn-danger',
// 				buttonsStyling: false
// 			}).then(function () {
// 					if (confirm) {
// 					   $.ajax({
// 						type: "GET",
// 						url: "{{route('deletemasterproduct', $row->id)}}",
// 						success: function (data) {
// 							setInterval(function () {
// 									window.location.href = "/home";
// 							   }, 3000);
// 							}
// 						});
// 					swal(
// 							'Deleted !',
// 							'Product Deleted Sussfully! Redirecting .....! Please wait!!',
// 							'success'
// 						)
// 					}

// 				}, function (dismiss) {
// 					if (dismiss === 'cancel') {
// 						window.location.href = "{{ route('kits.edit',$row->id) }}";
// 						}
// 					}
// 				)
// 			}
// 		}
// 	)
// });

function GetModel(url){
	$.ajax({
		url:url,
		method:'GET',
		success:function(res){
			$("#MyModal").html(res);
			$("#MyModal").modal();
		}
	});
}

function disableEnableTextBox(value, id){
	if (!value.checked) {
		$("#" + id).attr("disabled", "disabled");	
		$("#" + id).val("");
	} else {
		$("#" + id).removeAttr("disabled");
	}
}

</script>

@endsection
