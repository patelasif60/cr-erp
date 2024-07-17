
<?php $__env->startSection('before-css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/dropzone.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('assets/custom/css/custom.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
<style>

form .form-group {
    margin-right: -5px;
    display: inline-block;
}

.select2-container--default .select2-selection--multiple{
	padding: 0 !important;
	letter-spacing: 0 !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered li{
	width: 100px !important;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="breadcrumb">
	<h1>Cranium</h1>
	<ul>
		<li><a href="<?php echo e(route('allmasterproductlsts')); ?>">All Master Product Listing</a></li>
		<li>ADD Kit Product</li>
	</ul>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="card">
	<div class="card-header bg-transparent">
		<div class="row">
			<div class="col-md-6">
				<h3 class="card-title"> ADD Kit Product</h3>
			</div>
			<div class="col-md-6">
				<div id="error_container"></div>
			</div>
		</div>
	</div>
<!--begin::form-->
	<form method="POST" action="<?php echo e(route('kits.store')); ?>" enctype="multipart/form-data" id="product_add">
	<?php echo csrf_field(); ?>
	<input type="hidden" name="item_form_description" id="item_form_description" value="Kit" >
		<div class="card-body ">
			<!----------===================================================------------>
			<div class="row col-lg-12">
				<div class="col-lg-8">
					<div class="form-group col-md-12">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="The product name displayed on stores and marketplaces">Product Listing Name<small>(Auto Generated)</small></label>
						<input type="text" class="form-control" id="product_listing_name" name="product_listing_name" placeholder="Product Listing Name" readonly>

					</div>
					<div class="form-group col-md-6">
						<label for="ETIN" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="e-tailer internal SKU">ETIN</label>
						<input type="text" class="form-control" id="ETIN" placeholder="ETIN" name ="ETIN" value='<?php echo e($newetin); ?>' readonly>
					</div>
					<div class="form-group col-md-6">
						<label for="status" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Current Product Status, i.e. Active, Deplete, Discontinued, Blocked, Pending">Status <span class="text-danger">*</span></label>
						<select id="status" name="status" class="form-control select2" >
							<option value=""> -- Select a value  -- </option>
							<?php if($product_status): ?>
								<?php $__currentLoopData = $product_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($row_status->product_status); ?>"><?php echo e($row_status->product_status); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="ETIN of how e-tailer purchases the item">Parent ETIN</label>
						<input type="text" class="form-control" id="parent_ETIN" placeholder="Parent ETIN" name ="parent_ETIN" readonly>
					</div>
					<div class="form-group col-md-6">
						<label for="etailer_availability" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Indicates whether the item is stocked in house, special order or dropshipped, i.e. Stocked, Special Order, Dropship">e-tailer Availability <span class="text-danger">*</span></label>
						<select id="etailer_availability" name="etailer_availability" class="form-control select2" id="etailer_availability" >
								<option value=''> -- Select a value  -- </option>
								<?php if($etailers): ?>
									<?php $__currentLoopData = $etailers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<?php endif; ?>

						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="alternate_ETINs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Previous ETINs used">Alternate ETINs</label>
						<input type="text" class="form-control" id="alternate_ETINs" placeholder="Alternate ETINs" name ="alternate_ETINs">
					</div>
					<div class="form-group col-md-6">
						<label for="product_temperature" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Product temperature category, i.e. Dry-Fragile, Frozen, Refrigerated, Dry-Strong">Product Temperature <span class="text-danger">*</span></label>
						<select id="product_temperature" name="product_temperature" class="form-control select2" id="product_temperature" >
								<option value='' selected> -- Select a value  -- </option>
							<?php if($producttemp): ?>
								<?php $__currentLoopData = $producttemp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producttemplist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($producttemplist); ?>"><?php echo e($producttemplist); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="product_listing_ETIN" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Listing SKU for 3PL Client Requirements">Product listing ETIN</label>
						<input type="text" class="form-control" id="product_listing_ETIN" name="product_listing_ETIN" placeholder="Product listing ETIN">
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
							<?php if($warehouse): ?>
								<?php $__currentLoopData = $warehouse; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
										<td><?php echo e($warehouses); ?></td>
										<td><input type="checkbox" name="warehouses_assigned[]" value="<?php echo e($warehouses); ?>"></td>
										<td></td>
									</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>
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
								<table class="table table-border table-stripped dataTable_filter" id="parent_products">
									<thead>
										<tr>
											<td>ETIN</td>
											<td>Brand</td>
											<td>Product Listing Name</td>
											<td>UPC</td>
											<td>Action</td>
										</tr>
									</thead>
									<!-- <thead>
										<tr>
											<td>
												<select name="etin_filter[]" id="etin_filter" class="form-control select2 " multiple>
													<option value="">Select</option>

													<?php $__currentLoopData = $getet; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_etin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($row_etin); ?>"><?php echo e($row_etin); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
											</td>
											<td>
												<select  id="brand_filter" name="brand_filter[]" class="form-control select2" multiple>
													<option value=''>Select</option>
													<?php $__currentLoopData = $brand; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brandlist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($brandlist); ?>"><?php echo e($brandlist); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
											</td>
											<td>
												<select id="product_list_filter" name="product_list_filter[]" class="form-control select2" multiple>
													<option value=''>Select</option>
													<?php $__currentLoopData = $product_listing_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product_list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($product_list); ?>"><?php echo e($product_list); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
											</td>
											<td>
												<select id="upc_filter" name="upc_filter[]" class="form-control select2" multiple>
													<option value=''>Select</option>
													<?php $__currentLoopData = $upcs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($upc); ?>"><?php echo e($upc); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
											</td>
											<td></td>
										</tr>
									</thead> -->
								</table>
							</div>
							<div class="col-lg-5">
								<p><b>Selected Components</b></p>
								<input type="hidden" id="selected_products">
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
									<input type="text" class="form-control" id="flavor" name="flavor" placeholder="Flavor Variation">
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
										<?php if($producttype): ?>
											<?php $__currentLoopData = $producttype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producttypelist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($producttypelist); ?>"><?php echo e($producttypelist); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</select>
									<a href="#" data-toggle="modal" data-target="#new_product_type_request">New Request</a>
								</div>
								<div class="form-group col-md-12">
									<label for="cost" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Product cost from supplier: (Case Cost / Case Count) x Pack Form">Cost <span class="text-danger">*</span></label>
									<input type="number" class="form-control" id="cost" name="cost" placeholder="Cost" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$">
								</div>

                                <div class="form-group col-md-12">
									<label for="acquisition_cost" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Acquisition Cost">Acquisition Cost</label>
									<input type="number" class="form-control" id="acquisition_cost" name="acquisition_cost" placeholder="Acquisition Cost" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$">
								</div>

								<div class="form-group col-md-12">
									<label for="new_cost" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="New Product cost from supplier">New Cost</label>
									<input type="number" class="form-control" id="new_cost" name="new_cost" placeholder="New Cost" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$">
								</div>

								<div class="form-group col-md-12">
									<label for="new_cost_date" class="ul-form__label" data-toggle="tooltip" data-placement="top"title="Date the new cost goes in effect">New Cost Date </label>
									<input type="date" class="form-control" id="new_cost_date" name="new_cost_date" placeholder="New Cost Date" >
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
													<input type="number" class="form-control text-center" id="weight" name="weight" placeholder="Weight (lbs)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" >
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Length of the item/unit in inches (in)">
													Length (in)
												</td>
												<td>
													<input type="number" class="form-control text-center" id="length" name="length" placeholder="Length (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" >
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Width (depth)of the item/unit in inches (in)">
													Width (in)
												</td>
												<td>
													<input type="number" class="form-control text-center" id="width" name="width" placeholder="Width (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" >
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Height of the item/unit in inches (in)">
													Height (in)
												</td>
												<td>
													<input type="number" class="form-control text-center" id="height" name="height" placeholder="Height (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" >
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
														<option value="Yes">Yes</option>
														<option value="No">No</option>
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
														<option value="Yes">Yes</option>
														<option value="No">No</option>
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
							<div class="col-md-3">
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Description of the product to be displayed on stores and marketplaces">Full Product Descrtiption <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="full_product_desc" name="full_product_desc" placeholder="Full Product Descrtiption" >
								</div>
								<div class="form-group col-md-12">
									<label for="about_this_item" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Bullet points highlighting the item in Amazon's 'About this item' section">About This Item</label>
									<input type="text" class="form-control  mb-3" id="about_this_item_1" name="about_this_item[]" placeholder="Point 1">
									<input type="text" class="form-control  mb-3" id="about_this_item_2" name="about_this_item[]" placeholder="Point 2">
									<input type="text" class="form-control  mb-3" id="about_this_item_3" name="about_this_item[]" placeholder="Point 3">
									<input type="text" class="form-control  mb-3" id="about_this_item_4" name="about_this_item[]" placeholder="Point 4">
									<input type="text" class="form-control mb-3" id="about_this_item_5" name="about_this_item[]" placeholder="Point 5">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Top product category of the hierarchy, i.e. Heat & Serve Meals">Product Category <span class="text-danger">*</span></label>
									<select id="product_category" name="product_category" class="form-control select2 all_product_category" id="product_category" data-id='0'>
											<option value=''> -- Select a value  -- </option>
										<?php if($categories): ?>
											<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($row_cat->id); ?>"><?php echo e($row_cat->name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</select>
								</div>
								<div id="next_product_container_0">

								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="i.e. Gluten-Free, Vegetarian, Low-fat">Key Product Attributes & Diet</label>
									<textarea type="text" class="form-control" id="key_product_attributes_diet" name="key_product_attributes_diet" placeholder="Key Product Attributes & Diet" readonly></textarea>
								</div>

							</div>
							<div class="col-md-6">
								<!-- <div class="form-group col-md-12">
									<label for="product_tags"  data-toggle="tooltip" data-placement="top" title="Product tags/groups for filtering and identification, i.e. Gluten-Free, Vegetarian, Low-fat, good for you, Hospitality-resort, Hospitality-Urban, etc.">Product Tags</label>
									<textarea id="product_tags" class="form-control" name="product_tags" readonly></textarea>
								</div> -->
								<div class="form-group col-md-6">
									<label for="product_tags"  data-toggle="tooltip" data-placement="top" title="Product tags/groups for filtering and identification, i.e. Gluten-Free, Vegetarian, Low-fat, good for you, Hospitality-resort, Hospitality-Urban, etc.">Product Tags Not Assigned</label>
									<div class="custom_one_line_cards_container ProductTagsDrop border">
										<?php if($producttag): ?>
											<?php $__currentLoopData = $producttag; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<div class="product_tags_cards custom_one_line_cards" id="<?php echo e($key); ?>"><?php echo e($value); ?></div>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</div>
								</div>
								<div class="form-group col-md-6">
									<input type="hidden" name="product_tags" id="product_tags">
									<label for="lobs" data-toggle="tooltip" data-placement="top" title="Product tags/groups for filtering and identification, i.e. Gluten-Free, Vegetarian, Low-fat, good for you, Hospitality-resort, Hospitality-Urban, etc.">Product Tags Assigned</label>
									<div class="custom_one_line_cards_container ProductTagsDropAssigned border">
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- <div class="tab-pane fade " id="tab_supplier" role="tabpanel" area-labelledby="supplier_tab">
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
												<td><input type="number" class="form-control" id="cost" name="cost" placeholder="Cost" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$" ></td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="New Product cost from supplier">
													New Cost
												</td>
												<td><input type="number" class="form-control" id="new_cost" name="new_cost" placeholder="New Cost" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$"></td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Date the new cost goes in effect">
													New Cost Date
												</td>
												<td><input type="date" class="form-control" id="new_cost_date" name="new_cost_date" placeholder="New Cost Date"></td>
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
													<input type="text" class="form-control text-center" id="asin" name="asin" placeholder="ASIN">
												</td>
											</tr>

										</tbody>
									</table>
								</div>
							</div>


						</div>
					</div> -->

					<div class="tab-pane fade " id="tab_images" role="tabpanel" area-labelledby="images_tab">
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
										<?php if($image_types): ?>
											<?php $__currentLoopData = $image_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($image_type->image_type); ?>"><?php echo e($image_type->image_type); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
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
									<input type="text" class="form-control" id="ingredients" name="ingredients" placeholder="Ingredients" readonly/>
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
								<div class="form-group col-md-12">
									<label for="allergens" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Known allergens">Allergens</label>
									<input type="text" class="form-control" id="allergens_names" name="allergens_names" placeholder="Allergense" readonly>
									<input type="hidden" class="form-control" id="allergens" name="allergens" placeholder="Allergense" readonly>
								</div>
								<!-- <div class="form-group col-lg-6">
									<label for="allergens" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="List of known allergens">Allergens Not Assigned</label>
									<div class="custom_one_line_cards_container AllergensDrop border">
										<?php if($allergens): ?>
											<?php $__currentLoopData = $allergens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_allergens): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<div class="allergens_cards custom_one_line_cards" id="<?php echo e($row_allergens); ?>"><?php echo e($row_allergens); ?></div>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</div>
								</div>
								<div class="form-group col-lg-6">
									<input type="hidden" name="allergens" id="allergens">
									<label for="allergens" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="List of known allergens">Allergens Assigned </label>
									<div class="custom_one_line_cards_container AllergensDropAssigned border">
									</div>
								</div> -->
							</div>
						</div>
					</div>



					<div class="tab-pane fade " id="tab_history" role="tabpanel" area-labelledby="history_tab">
						<div class="row">
							<div class="form-group col-md-12">
							<br>
								<h3 class="card-title text-center">Product History</h3>
								<input type="text" class="form-control text-center" id="duplicate_full_product_desc" name="duplicate_full_product_desc" placeholder="Product History">
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
									<div class="custom_one_line_cards_container LobsDrop border">
										<?php if($client): ?>
											<?php $__currentLoopData = $client; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if(isset($req['client_id'])): ?>
												<?php else: ?>
													<div class="lobs_cards custom_one_line_cards" id="<?php echo e($key); ?>"><?php echo e($value); ?></div>
												<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</div>
								</div>
								<div class="form-group col-md-6">
									<input type="hidden" name="lobs" id="lobs" value="<?php if(isset($req['client_id'])): ?><?php echo e($req['client_id']); ?><?php endif; ?>">
									<label for="lobs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Clients & Sites Assigned <span class="text-danger">*</span></label>
									<div class="custom_one_line_cards_container LobsDropAssigned border">
										<?php if($client): ?>
											<?php $__currentLoopData = $client; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if(isset($req['client_id'])): ?>
													<?php if($req['client_id'] == $key): ?>
														<div class="lobs_cards custom_one_line_cards" id="<?php echo e($key); ?>"><?php echo e($value); ?></div>
													<?php endif; ?>
												<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
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
													<input type="text" class="form-control text-center" placeholder="Supplier Product Number" id="supplier_product_number" name="supplier_product_number">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Manufacturer's product number">
													Manufacturer Product Number
												</td>
												<td>
													<input type="text" class="form-control text-center" placeholder="Manufacturer Product Number" id="manufacture_product_number" name="manufacture_product_number">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="12 Digit UPC (Universal Product Code) of the item/unit being sold">
													UPC
													<!-- <span class="text-danger">*</span> -->
												</td>
												<td>
													<input type="text" class="form-control text-center" id="upc" name="upc" placeholder="UPC" minlength="12" maxlength="12" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='validatetext(event)'>
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="UPC Scanable">UPC Scanable</td>
												<td>
													<input type="checkbox" id="upc_scanable" name="upc_scanable" checked />
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="14 Digit case GTIN (Global Trade Identification Number) of the item/unit being sold">
													GTIN
													<!-- <span class="text-danger">*</span> -->
												</td>
												<td>
													<input type="text" class="form-control text-center" id="gtin" name="gtin" placeholder="GTIN" minlength="14" maxlength="14" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='validatetext(event)'>
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="GTIN Scanable">GTIN Scanable</td>
												<td>
													<input type="checkbox" id="gtin_scanable" name="gtin_scanable" checked />
												</td>
											</tr>
											
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Amazon Standard Identification Number">
													ASIN
												</td>
												<td>
													<input type="text" class="form-control text-center" id="asin" name="asin" placeholder="ASIN">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Global Product Classification Code for international shipping etc.">
													GPC Code
												</td>
												<td>
													<input type="text" class="form-control text-center" id="GPC_code" name="GPC_code" placeholder="GPC Code">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Global Product Classification Category for international shipping etc.">
													GPC Class
												</td>
												<td>
													<input type="text" class="form-control text-center" id="GPC_class" name="GPC_class" placeholder="GPC Class">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Harmonized System for International shipping">
													HS Code
												</td>
												<td>
													<input type="text" class="form-control text-center" id="HS_code" name="HS_code" placeholder="HS Code">
												</td>
											</tr>
										</tbody>
									</table>
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
						<a href="<?php echo e(route('allmasterproductlsts')); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
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
<div id="new_product_type_request" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header-->
			<div class="modal-header" style="background-color:#fff;">
				<h3>New Product Type Request</h3>
				<!--Close/Cross Button-->
				<button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
			</div>
			<form  method="POST" action="javascript:void(0)" id="add_new_product_type_request_form" >
				<?php echo csrf_field(); ?>
				<div class="modal-body">
					<label>Product Type</label>
					<input type="text" name="request_field" class="form-control" id="request_field" style="width:100%;"/>
				</div>
				<div class="modal-footer">

					<button type="submit" class="btn btn-primary" id="add_manufacturer">Add</button>
					<a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
 <script src="<?php echo e(asset('assets/js/vendor/dropzone.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/dropzone.script.js')); ?>"></script>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('bottom-js'); ?>


<script>


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

	$("#product_add").validate({
		submitHandler(form){
        $('div#preloader').show();
			// var component_count = $("#selected_products").val();
			// alert(component_count);
			$(".submit").attr("disabled", true);
			var form_cust = $('#product_add')[0];
			let form1 = new FormData(form_cust);
			$.ajax({
				type: "POST",
				url: '<?php echo e(route('kits.store')); ?>',
				data: form1,
				processData: false,
				contentType: false,
				success: function( response ) {
                    $('div#preloader').hide();
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
                    $('div#preloader').hide();
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

<script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
<script type="text/javascript">
  $(function () {
	GetParentProducts();
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


$(document).on('change','.all_product_category',function () {
	 var id = $(this).val();
	 var level = $(this).data('id');
	 var myurl = "<?php echo e(url('getsubcategories')); ?>" +"/"+ id;
	 var token = "<?php echo e(csrf_token()); ?>";

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

  function GetParentProducts(){
	var table = $('#parent_products').DataTable({
		destroy:true,
        processing: true,
		ordering: true,
	    serverSide: true,
		search: {
			"search": $('#search_dt').val()
		},
        ajax: {
			url: "<?php echo e(route('GetAllParentApprovedProducts')); ?>",
			method:'GET',
			data: function(d){
				d.ids = $("#selected_products").val();
				d.etin_filter = $("#etin_filter").val();
				d.brand_filter = $("#brand_filter").val();
				d.product_list_filter = $("#product_list_filter").val();
				d.upc_filter = $("#upc_filter").val();
				d.client_id = <?php if(isset($req['client_id'])): ?> '<?php echo e($req['client_id']); ?>' <?php else: ?> '' <?php endif; ?>
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
	$('.ProductTagsDropAssigned').empty();
	$('.LobsDropAssigned').empty();
	$("#product_tags").val("");
    $.ajax({
        url: '<?php echo e(route('GetSelectedProductForKit')); ?>',
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
			$("#product_tags").val(data.product_tags_array);
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
	html+='<div class="col-md-3"><label for="" class="ul-form__label">Image Type</label><select class="form-control" name="image['+i+'][image_type]" id="image_type'+i+'"><option value="">Please Select</option><?php if($image_types): ?><?php $__currentLoopData = $image_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($image_type->image_type); ?>"><?php echo e($image_type->image_type); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php endif; ?></select></div>';
	html+='<div class="col-md-1"><button type="button" class="btn btn-danger" id="remove_about" onclick="RemoveRow('+i+')" style="margin-top:40px;"><i class="far fa-window-close"></i></button></div></div>';
	console.log(html);
	$("#about_append").append(html);

	$("#image_type"+i).select2();

}

function RemoveRow(id){
	$("#row"+id).remove();
}

$("#add_new_product_type_request_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#add_new_product_type_request_form')[0];
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '<?php echo e(route('new_product_type_kit_request')); ?>',
            data: form1,
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error == 0){
					$(".submit").attr("disabled", false);
                    toastr.success(response.msg);
					$("#new_product_type_request").modal('hide');
                }else{
                    $(".submit").attr("disabled", false);
                    toastr.error(response.msg);
                }
            },
            error: function(data){
                $(".submit").attr("disabled", false);
                var errors = data.responseJSON;
                $.each( errors.errors, function( key, value ) {
                    var ele = "#"+key;
                    $(ele).addClass('error');
                    $('<label class="error">'+ value +'</label>').insertAfter(ele);
                });
          }
        })
        return false;
    }
});

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/cranium/kitproducts/create.blade.php ENDPATH**/ ?>