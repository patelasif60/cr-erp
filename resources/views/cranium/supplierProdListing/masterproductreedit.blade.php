@extends('layouts.master')
@section('before-css')

<style>
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
    height: 350px;
    width: 350px;
}
.col-md-3.column.single-image {
    padding: 10px 0px 25px 25px;
	flex: 4 4 20%;
}
</style>
   <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
@endsection

@section('main-content')

<div class="card">
	<div class="card-header bg-transparent">
		<div class="col-md-6" style="float: left;">
			<h3 class="card-title"> Re-Editing Master Product</h3>
		</div>

		<div class="btn btn-success m-1"  style="float: right; text-align: right;">

			<span class="ul-btn__icon"><i class="i-Gear-2"></i></span>
			<a href="{{ url('home') }}" class="ul-btn__text" style="color:#fff;"> Reurn to Product listing</a>
		</div>
	</div>
<!--begin::form-->

	<div direction="left" class="marqueeclass" id="hylighttext">Re-Edit will move this product to Approval Queue. </div>

	<form method="POST" action="{{ route('reupdatemasterproduct') }}" enctype="multipart/form-data">
	@csrf
	@method('put')
		<div class="card-body">
			<input type="hidden" value="{{$productdetails->id}}" name='id' id="id">

<!-------===================================================--------->
			<div class="card">
				<div class="form-group col-md-12">
					<div class="form-group col-md-4">
						<label for="inputEmail4" class="ul-form__label">ETIN</label>
						<div class="input-group mb-3">
							<input type="text" class="form-control" id="ETIN" placeholder="ETIN" name ="ETIN" value="{{$productdetails->ETIN}}" >
						</div>
					</div>
					<div class="form-group col-md-4">
						<label for="inputEmail4" class="ul-form__label">Product Listing Name</label>
						<div class="input-group mb-3">
							<input type="text" class="form-control" id="product_listing_name" name="product_listing_name" placeholder="Product Listing Name" value="{{$productdetails->product_listing_name}}" readonly>
						</div>
					</div>
					<div class="form-group col-md-4">
						<label for="inputEmail4" class="ul-form__label">Full Product Descrtiption</label>
						<div class="input-group mb-3">
							<input type="text" class="form-control" id="full_product_desc" name="full_product_desc" placeholder="Full Product Descrtiption" value="{{$productdetails->full_product_desc}}" >
						</div>
					</div>
				</div>
				<div class="form-group col-md-12">
					<div class="form-group col-md-4">
						<label for="inputEmail4" class="ul-form__label">Parent ETIN</label>
						<div class="input-group mb-3">
							<input type="text" class="form-control" id="parent_ETIN" placeholder="Parent ETIN" name ="parent_ETIN" value="{{$productdetails->parent_ETIN}}" readonly>
						</div>
					</div>
					<div class="form-group col-md-4">
						<label for="inputEmail4" class="ul-form__label">Supplier Description</label>
						<div class="input-group mb-3">
							<input type="text" class="form-control" id="item_form_description" name="item_form_description" placeholder="Supplier Description" value="{{$productdetails->item_form_description}}">
						</div>
					</div>
					<div class="form-group col-md-4">
						<label for="inputEmail4" class="ul-form__label">Product Listing Name</label>
						<div class="input-group mb-3">
							<input type="text" class="form-control" id="product_listing_name" name="product_listing_name" placeholder="Product Listing Name" value="{{$productdetails->product_listing_name}}">
						</div>
					</div>
				</div>
			</div>
			<br>
			<div class="form-group col-md-12 card">
				<div class="form-group col-md-3" style="vertical-align: top;">
					<label for="inputEmail4" class="ul-form__label">Status</label>
					<div class="input-group mb-3">
						<input type="text" class="form-control" id="status" name="status" placeholder="Status" value="{{$productdetails->status}}">
					</div>

					<div class="card-body">

						<div class="table-responsive card">
							<table class="table table-hover">
								<thead>
									<tr>
										<td colspan="2"><h3 class="card-title text-center">Supplier Details</h3></td>
									</tr>
								</thead>
							<tbody>
								<tr>
									<th scope="row">Current Supplier</th>
									<td>
										<div class="input-group mb-3">
											<select id="current_supplier" name="current_supplier" class="form-control" id="current_supplier" required>
													<option value=''> -- Select a value  -- </option>
												@foreach($supplier as $key=>$value)
													<option value="{{ $key }}" <?php if($productdetails->client_supplier_id == $key ) echo "selected";?>>{{ $value }}</option>
												@endforeach
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row">Supplier Status</th>
									<td>{{$productdetails->status}}</td>
								</tr>
								<tr>
									<th scope="row">Alternate Supplier(s)</th>
									<td>N.A.</td>
								</tr>
								<tr>
									<th scope="row">Dropship Available</th>
									<td>{{$productdetails->etailer_availability}}</td>
								</tr>
								<tr>
									<th scope="row">Cost</th>
									<td>{{$productdetails->cost}}</td>
								</tr>
                                <tr>
									<th scope="row">Acquisition Cost</th>
									<td>{{$productdetails->acquisition_cost}}</td>
								</tr>
								<tr>
									<th scope="row">New Cost</th>
									<td>{{$productdetails->new_cost}}</td>
								</tr>
								<tr>
									<th scope="row">New Cost Date</th>
									<td>{{$productdetails->new_cost_date}}</td>
								</tr>
							</tbody>
							</table>
						</div>
					</div>

					<div class="card-body">
						<div class="table-responsive card">
							<table class="table table-hover">
								<thead>
									<tr>
										<td colspan="2"><h3 class="card-title text-center">Product Codes</h3></td>
									</tr>
								</thead>
							<tbody>
								<tr>
									<th scope="row">Supplier Product</th>
									<td>
										<input type="text" class="form-control text-center" placeholder="Supplier Product" value="">
									</td>
								</tr>
								<tr>
									<th scope="row">Manufacturer</th>
									<td>{{$productdetails->manufacturer}}</td>
								</tr>
								<tr>
									<th scope="row">UPC</th>
									<td>
										<input type="text" class="form-control text-center" id="upc" name="upc" placeholder="UPC" value="{{$productdetails->upc}}">
									</td>
								</tr>
								<tr>
									<td scope="row" data-toggle="tooltip" data-placement="top" title="UPC Scanable">UPC Scanable</td>
									<td>
										<input type="checkbox" id="upc_scanable" name="upc_scanable" <?php if ($productdetails->upc_scanable == "1") { echo 'checked'; }?>/>
									</td>
								</tr>
								<tr>
									<th scope="row">GTIN</th>
									<td>
										<input type="text" class="form-control text-center" id="gtin" name="gtin" placeholder="GTIN" value="{{$productdetails->gtin}}">
									</td>
								</tr>
								<tr>
									<td scope="row" data-toggle="tooltip" data-placement="top" title="GTIN Scanable">GTIN Scanable</td>
									<td>
										<input type="checkbox" id="gtin_scanable" name="gtin_scanable" <?php if ($productdetails->gtin_scanable == "1") { echo 'checked'; }?>/>
									</td>
								</tr>
								<tr>
									<th scope="row">ASIN</th>
									<td>
										<input type="text" class="form-control text-center" id="asin" name="asin" placeholder="ASIN" value="{{$productdetails->asin}}">
									</td>
								</tr>
								<tr>
									<th scope="row">GPC Code</th>
									<td>
										<input type="text" class="form-control text-center" id="GPC_code" name="GPC_code" placeholder="GPC Code" value="{{$productdetails->GPC_code}}">
									</td>
								</tr>
								<tr>
									<th scope="row">GPC Class</th>
									<td>
										<input type="text" class="form-control text-center" id="GPC_class" name="GPC_class" placeholder="GPC Class" value="{{$productdetails->GPC_class}}">
									</td>
								</tr>
								<tr>
									<th scope="row">HS Code</th>
									<td>
										<input type="text" class="form-control text-center" id="HS_code" name="HS_code" placeholder="HS Code" value="{{$productdetails->HS_code}}">
									</td>
								</tr>
							</tbody>
							</table>
						</div>
					</div>

					<div class="card-body">
						<div class="table-responsive card">
							<table class="table table-hover">
								<thead>
									<tr>
										<td colspan="2"><h3 class="card-title text-center">Product Dimensions Case</h3></td>
									</tr>
								</thead>
							<tbody>
								<tr>
									<th scope="row">Weight (lbs)</th>
									<td>
										<input type="text" class="form-control text-center" id="weight" name="weight" placeholder="Weight (lbs)" value="{{$productdetails->weight}}">
									</td>
								</tr>
								<tr>
									<th scope="row">Length (in)</th>
									<td>
										<input type="text" class="form-control text-center" id="length" name="length" placeholder="Length (in)" value="{{$productdetails->length}}">
									</td>
								</tr>
								<tr>
									<th scope="row">Width (in)</th>
									<td>
										<input type="text" class="form-control text-center" id="width" name="width" placeholder="Width (in)" value="{{$productdetails->width}}">
									</td>
								</tr>
								<tr>
									<th scope="row">Height (in)</th>
									<td>
										<input type="text" class="form-control text-center" id="height" name="height" placeholder="Height (in)" value="{{$productdetails->height}}">
									</td>
								</tr>
							</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="form-group col-md-9">
					<div class="form-group col-md-12">
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">e-tailer Availability</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control"  id="availability" name="availability" placeholder="e-tailer Availability" value="{{$productdetails->etailer_availability}}">
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Warehouse(s) Assigned</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="warehouses_assigned" name="warehouses_assigned" placeholder="Warehouse(s) Assigned" value="{{$productdetails->warehouses_assigned}}">
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Clients & Sites Assigned</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="warehouses_assigned" name="warehouses_assigned" placeholder="Clients & Sites Assigned" value="{{$productdetails->warehouses_assigned}}">
							</div>
						</div>
					</div>
					<div class="form-group col-md-12">
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Manufacturer</label>
							<div class="input-group mb-3">
								<select id="manufacturer" name="manufacturer" class="form-control" id="categories" required>
										<option value=''> -- Select a value  --</option>
									@foreach($manufacturer as $manufacturerlist)
										<option value="{{ $manufacturerlist }}" <?php if($productdetails->manufacturer == $manufacturerlist ) echo "selected";?>>{{ $manufacturerlist }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Brand</label>
							<div class="input-group mb-3">
								<select  id="brand" name="brand" class="form-control" id="categories" required>
										<option value=''> -- Select a value  --</option>
									@foreach($brand as $brandlist)
										<option value="{{ $brandlist }}" <?php if($productdetails->brand == $brandlist ) echo "selected";?>>{{ $brandlist }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Flavor Variation</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="flavor" name="flavor" placeholder="Flavor Variation" value="{{$productdetails->flavor}}">
							</div>
						</div>
					</div>
					<div class="form-group col-md-12">
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Product Type</label>
							<div class="input-group mb-3">
								<!--<input type="text" class="form-control" id="product_type" name="product_type" placeholder="Product Type" value="{{$productdetails->product_type}}" required>-->
								<select id="product_type" name="product_type" class="form-control" id="product_type" required>
									<option value=''> -- Select a value  -- </option>
									@foreach($producttype as $producttypelist)
										<option value="{{ $producttypelist }}" <?php if($productdetails->product_type == $producttypelist ) echo "selected";?>>{{ $producttypelist }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="unit_list" class="ul-form__label">Unit Size</label>
							<div class="input-group mb-3">
								<input type="number" id="unit_num" name="unit_num" class="form-control" value="{{$productdetails->unit_num}}" placeholder="Select Unit Count" required>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<select id="unit_list" name="unit_list" class="form-control" id="unit_list" required>
									<option value='' selected> -- Select unit -- </option>
									@foreach($unitsize as $unitsizelist)
										<option value="{{ $unitsizelist }}"<?php if($productdetails->unit_list == $unitsizelist ) echo "selected";?>>{{ $unitsizelist }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Unit Description</label>
							<div class="input-group mb-3">
								<!--<input type="text" class="form-control" id="unit_description" name="unit_description" placeholder="Unit Description" value="{{$productdetails->unit_description}}" required>-->
								<select id="unit_description" name="unit_description" class="form-control" id="unit_description" required>
									<option value=''> -- Select a value  -- </option>
									@foreach($unitdesc as $unitdesclist)
										<option value="{{ $unitdesclist }}" <?php if($productdetails->unit_description == $unitdesclist ) echo "selected";?>>{{ $unitdesclist }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="form-group col-md-12">
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Pack Form Count</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="pack_form_count" name="pack_form_count" placeholder="Pack Form Count" value="{{$productdetails->pack_form_count}}">
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Units in Pack</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="package_information" name="package_information" placeholder="Units in Pack" value="{{$productdetails->package_information}}">
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Item Form Description</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="item_form_description" name="item_form_description" placeholder="Item Form Description" value="{{$productdetails->item_form_description}}" required>
							</div>
						</div>
					</div>
					<div class="form-group col-md-12">
						<div class="form-group col-md-12">
							<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Top product category of the hierarchy, i.e. Heat & Serve Meals">Product Category <span class="text-danger">*</span></label>
							<select id="product_category" name="product_category" class="form-control select2 all_product_category <?php if(isset($producthistory)){ if($producthistory->product_category != $productdetails->product_category) echo "edited-field";}?> @if(ProductEditPermission('product_category') == 0) custom_readonly @endif" id="product_category"  data-id='0'>
								<option value=''> -- Select a value  -- </option>
								@if ($categories)
									@foreach($categories as $row_cat)
										<option value="{{ $row_cat->id }}" <?php if($productdetails->product_category == $row_cat->id ) echo "selected";?>>{{ $row_cat->name }}</option>
									@endforeach
								@endif
							</select>
						</div>


						<div id="next_product_container_0">
							@for($i=1; $i<=9;$i++)
								<?php $name = 'product_subcategory'.$i;?>
								@if($productdetails->$name != '')
									<div class="form-group col-md-12">
										<label for="{{$name}}" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="2nd product category of the hierarchy, i.e. Frozen Meals">Product Sub-Category {{$i}}</label>
										<select id="{{$productdetails->$name}}" name="{{$productdetails->$name}}" class="form-control select2 <?php if(isset($producthistory)){ if($producthistory->$name != $productdetails->$name) echo "edited-field";}?> @if(ProductEditPermission($name) == 0) custom_readonly @endif" id="{{$name}}" >
											<option value="">Select</option>
											<option value='{{$productdetails->$name}}' selected> {{CategoryName($productdetails->$name)}} </option>
										</select>
									</div>
									<div id="next_product_container_{{$i}}"></div>
								@endif
							@endfor
						</div>
					</div>

					<div class="form-group col-md-12">
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Key Product Attributes & Diet</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="key_product_attributes_diet" name="key_product_attributes_diet" placeholder="Key Product Attributes & Diet" value="{{$productdetails->key_product_attributes_diet}}">
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Ingredients</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="ingredients" name="ingredients" placeholder="Ingredients" value="{{$productdetails->ingredients}}">
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Allergens</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="allergens" name="allergens" placeholder="Allergens" value="{{$productdetails->allergens}}">
							</div>
						</div>
					</div>
					<div class="form-group col-md-12">
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Prop 65 Flag</label>
							<div class="input-group mb-3">
								<!--<input type="text" class="form-control" id="prop_65_flag" name="prop_65_flag" placeholder="Prop 65 Flag" value="{{$productdetails->prop_65_flag}}">-->
								<select id="prop_65_flag" name="prop_65_flag" class="form-control" id="prop_65_flag" required>
										<option value=''> -- Select a value  -- </option>
										<option value='yes' <?php if($productdetails->prop_65_flag == 'yes' ) echo "selected";?>> Yes </option>
										<option value='no' <?php if($productdetails->prop_65_flag == 'no' ) echo "selected";?>> No </option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Prop 65 Ingredient(s)</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="Prop_65_ingredient" name="prop_65_ingredient" placeholder="Prop 65 Ingredient(s)" value="{{$productdetails->prop_65_ingredient}}">
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Hazardous Materials</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="hazardous_materials" name="hazardous_materials" placeholder="Hazardous Materials" value="{{$productdetails->hazardous_materials}}">
							</div>
						</div>
					</div>
					<div class="form-group col-md-12">
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Storage</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control"id="storage" name="storage" placeholder="Storage" value="{{$productdetails->storage}}">
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">MFG Shelf Life</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="MFG_shelf_life" name="MFG_shelf_life" placeholder="MFG Shelf Life" value="{{$productdetails->MFG_shelf_life}}">
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Country of Origin</label>
							<div class="input-group mb-3">
								<!--<input type="text" class="form-control" id="country_of_origin" name="country_of_origin" placeholder="Country of Origin" value="{{$productdetails->country_of_origin}}" required>-->
								<select id="country_of_origin" name="country_of_origin" class="form-control" id="country_of_origin" required>
										<option value=''> -- Select a value  -- </option>
									@foreach($country as $countrylist)
										<option value="{{ $countrylist }}" <?php if($productdetails->country_of_origin == $countrylist ) echo "selected";?>>{{ $countrylist }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="form-group col-md-12">
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Package Information</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="package_information" name="package_information" placeholder="Package Information" value="{{$productdetails->package_information}}">
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">POG Item</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="POG_flag" name="POG_flag" placeholder="POG Item" value="{{$productdetails->POG_flag}}">
							</div>
						</div>
						<div class="form-group col-md-4">
							<label for="inputEmail4" class="ul-form__label">Consignment Product</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="consignment" name="consignment" placeholder="Consignment Product" value="{{$productdetails->consignment}}">
							</div>
						</div>
					</div>
					<div class="form-group col-md-12">
						<div class="form-group col-md-2"></div>
						<div class="form-group col-md-8">
							<label for="inputEmail4" class="ul-form__label">Channel Listing Restrictions</label>
							<div class="input-group mb-3">
								<input type="text" class="form-control" id="channel_listing_restrictions" name="channel_listing_restrictions" placeholder="Channel Listing Restrictions" value="{{$productdetails->channel_listing_restrictions}}">
							</div>
						</div>
						<div class="form-group col-md-2"></div>
					</div>
				</div>
			</div>
			<br>

			<br>
			<div class="form-group col-md-12 card">
				<div class="form-group col-md-12">
				<br>
					<h3 class="card-title text-center">Product History</h3>
					<div class="input-group mb-3">
						<input type="text" class="form-control text-center" id="full_product_desc" name="full_product_desc" placeholder="Product History" value="{{$productdetails->full_product_desc}}">
					</div>
				</div>
			</div>
			<br>
			<div class="form-group col-md-12 card img-parent-div">
				<br>
				<h3 class="card-title text-center">Previous Images </h3>

				<div class="form-group col-md-12 custom-imgdiv">

				@if($productimg->isEmpty())
					<div class="row">
						<div class="column single-image" style="margin-left: 40%;">
							<img src= '{{url("/assets/images/no_img.png")}}' alt="No Image Available">
						</div>
					</div>
				@else

					<div class="row">

					@foreach($productimg[0] as $singleImg)
						@if($singleImg)
							<div class="col-md-3 column single-image">
								<img src= '{{ $singleImg }}' >
							</div>
						@endif
					@endforeach
				</div>
				@endif

				</div>
			</div>

			<div class="form-group col-md-12 card">
				<div class="col-md-6 mb-4" style="padding-top: 20px; margin-left: 25%;">
					<div class="card text-left">
						<div class="card-body">
							<h4 class="card-title">Upload NEW Product Images</h4>
							<input type="file" name="file[]" class="dropzone" multiple style="width: 100%;">
							<div><i>Note: You can upload Maximum 10 Images</i></div>
						</div>
					</div>
				</div>
			</div>



<!------------=================================------------------>
		</div>
		<div class="card-footer">
			<div class="mc-footer">
				<div class="row">
					<div class="col-lg-12 text-center">
						<button type="submit" class="btn  btn-primary m-1">Save</button>
						<button type="cancel" class="btn btn-outline-secondary m-1">Cancel</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
@endsection

@section('page-js')
<script>


$("button[type = 'submit']").click(function(){
	var $fileUpload = $("input[type='file']");
	if (parseInt($fileUpload.get(0).files.length) > 10){
		alert("You are only allowed to upload a maximum of 10 files");
		return false;
	}
});

$("#hylighttext").click(function(){
	swal(
		'You cannot Publish this product from here.',
		'Visit Master Product Edit to publish',
		'error'
	);
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

// Categoty - Subcategory dropdown

$('#product_category').change(function () {
	 var id = $(this).val();
	 var myurl = "{{url('getsubcategories')}}" +"/"+ id;
	 var token = "{{csrf_token()}}";


	 $.ajax({
		url:myurl,
		type:'GET',
		dataType:'json',
		data: {
			'_token': token,
			'_method': 'GET',
			'id' : id
			},
		success:function (response) {
			//alert(sucess);
			var len = 0;
			if (response.data != null) {
				len = response.data.length;
			}

			if (len>0) {
				for (var i = 0; i<len; i++) {
					 var id = response.data[i].id;
					 var name = response.data[i].sub_category_1;
					 var option = "<option value='"+id+"'>"+name+"</option>";
					 if(name != null )
						{
							$("#product_subcategory1").append(option);
						}

					 var id2 = response.data[i].id;
					 var name2 = response.data[i].sub_category_2;
					 var option2 = "<option value='"+id2+"'>"+name2+"</option>";
					 if(name2 != null )
						{
							$("#product_subcategory2").append(option2);
						}

					 var id3 = response.data[i].id;
					 var name3 = response.data[i].sub_category_3;
					 var option3 = "<option value='"+id3+"'>"+name3+"</option>";
					 if(name3 != null )
						{
							$("#product_subcategory3").append(option3);
						}
				}
			}
		}
	})
});
$('#product_category').focusout(function () {
	//alert('product_category1');
	var code1 = {};
	$("select[name='product_subcategory1'] > option").each(function () {
		if(code1[this.text]) {
			$(this).remove();
		} else {
			code1[this.text] = this.value;
		}
	});
	var code2 = {};
	$("select[name='product_subcategory2'] > option").each(function () {
		if(code2[this.text]) {
			$(this).remove();
		} else {
			code2[this.text] = this.value;
		}
	});
	var code3 = {};
	$("select[name='product_subcategory3'] > option").each(function () {
		if(code3[this.text]) {
			$(this).remove();
		} else {
			code3[this.text] = this.value;
		}
	});
});

$('#Prop_65_ingredient').prop('disabled', true);
$('#prop_65_flag').change(function () {
	$('#Prop_65_ingredient').val('');
	var propflag = $(this).val();
	if(propflag == 'yes'){
		$('#Prop_65_ingredient').prop('disabled', false);
	} else {
		$('#Prop_65_ingredient').prop('disabled', true);
	}
});
</script>
 <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
@endsection

@section('bottom-js')

@endsection
