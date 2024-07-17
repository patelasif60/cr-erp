
<?php $__env->startSection('before-css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/dropzone.min.css')); ?>">
<style>

form .form-group {
    margin-right: -5px;
    display: inline-block;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="breadcrumb">
	<h1>Cranium</h1>
	<ul>
		<li><a href="<?php echo e(route('allmasterproductlsts')); ?>">All Master Product Listing</a></li>
		<li><a href="<?php echo e(url('editmasterproduct/'.$productdetails->id)); ?>">Edit Master Product</a></li>
		<li>Adding Child Product</li>
	</ul>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="card">
	<div class="card-header bg-transparent">
		<div class="col-md-6" style="float: left;">
			<h3 class="card-title"> Adding Child Product of ETIN: <b> <?php echo e($productdetails->ETIN); ?> </b></h3>
		</div>
	</div>
<!--begin::form-->
	<form method="POST" action="<?php echo e(route('addchildproduct')); ?>" enctype="multipart/form-data" id="product_add">
	<?php echo csrf_field(); ?>
	<?php echo method_field('put'); ?>
		<div class="card-body ">
			<!----------===================================================------------>
			<div class="row col-lg-12">
				<div class="col-lg-8">
					<div class="form-group col-md-12">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="The product name displayed on stores and marketplaces">Product Listing Name<small>(Auto Generated)</small></label>
						<input type="text" class="form-control" id="product_listing_name" name="product_listing_name" placeholder="Product Listing Name"  value="<?php echo e($productdetails->product_listing_name); ?>" readonly>
					</div>
					<div class="form-group col-md-6">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="e-tailer internal SKU">ETIN</label>
						<input type="text" class="form-control" id="ETIN" placeholder="ETIN" name ="ETIN" value='<?php echo e($newetin); ?>' readonly>
					</div>
					<div class="form-group col-md-6">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Current Product Status, i.e. Active, Deplete, Discontinued, Blocked, Pending">Status <span class="text-danger">*</span></label>
						<select id="status" name="status" class="form-control select2" >
							<option value=""> -- Select a value  -- </option>
							<?php if($product_status): ?>
								<?php $__currentLoopData = $product_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($row_status->product_status); ?>" <?php if($productdetails->status == $row_status->product_status ) echo "selected";?>><?php echo e($row_status->product_status); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="ETIN of how e-tailer purchases the item">Parent ETIN</label>
						<input type="text" class="form-control" id="parent_ETIN" placeholder="Parent ETIN" name ="parent_ETIN" value="<?php echo e($productdetails->ETIN); ?>" readonly>
					</div>
					<div class="form-group col-md-6">
						<label for="etailer_availability" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Indicates whether the item is stocked in house, special order or dropshipped, i.e. Stocked, Special Order, Dropship">e-tailer Availability <span class="text-danger">*</span></label>
						<select id="etailer_availability" name="etailer_availability" class="form-control select2" id="etailer_availability" >
							<option value=''> -- Select a value  -- </option>
							<?php if($etailers): ?>
								<?php $__currentLoopData = $etailers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$etailer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($key); ?>"  <?php if($productdetails->etailer_availability == $key ) echo "selected";?> ><?php echo e($etailer); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="alternate_ETINs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Previous ETINs used">Alternate ETINs</label>
						<input type="text" class="form-control" id="alternate_ETINs" placeholder="Alternate ETINs" name ="alternate_ETINs" value="<?php echo e($productdetails->alternate_ETINs); ?>">
					</div>
					<div class="form-group col-md-6">
						<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Product temperature category, i.e. Dry-Fragile, Frozen, Refrigerated, Dry-Strong">Product Temperature <span class="text-danger">*</span></label>
						<select id="product_temperature" name="product_temperature" class="form-control <?php if(moduleacess('child_edit_tmp') == false): ?> custom_readonly <?php endif; ?>" id="product_temperature" >
							<option value='' selected> -- Select a value  -- </option>
							<?php if($producttemp): ?>
								<?php $__currentLoopData = $producttemp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producttemplist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($producttemplist); ?>" <?php if($productdetails->product_temperature == $producttemplist ) echo "selected";?>><?php echo e($producttemplist); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label for="product_listing_ETIN" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Listing SKU for 3PL Client Requirements">Product listing ETIN</label>
						<input type="text" class="form-control" id="product_listing_ETIN" name="product_listing_ETIN" placeholder="Product listing ETIN" value="<?php echo e($productdetails->product_listing_ETIN); ?>">
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
						<a class="nav-link active" href="#tab_product_detail" id="product_detail_tab" role="tab" aria-controls="product_detail_tab" area-selected="true" data-toggle="tab">Product Details</a>
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
							<div class="col-md-4">
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="The product's flavor or variety description">Flavor Variation</label>
									<input type="text" class="form-control" id="flavor" name="flavor" placeholder="Flavor Variation" value="<?php echo e($productdetails->flavor); ?>" readonly>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Base product name, i.e. Ice Cream, Energy Drink, Potato Chips, etc.">Product Type <span class="text-danger">*</span></label>
									<select id="product_type" name="product_type" class="form-control select2" >
										<option value='' selected> -- Select a value  -- </option>
										<?php if($producttype): ?>
											<?php $__currentLoopData = $producttype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producttypelist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($producttypelist); ?>" <?php if($productdetails->product_type == $producttypelist ) echo "selected";?>><?php echo e($producttypelist); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</select>
									<a href="#" data-toggle="modal" data-target="#new_product_type_request">New Request</a>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Count of packs per case">Pack Form Count <span class="text-danger">*</span></label>
									<input type="number" class="form-control" id="pack_form_count" name="pack_form_count" placeholder="Pack Form Count" min="1" >
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Count of units per pack">Units in Pack <span class="text-danger">*</span></label>
									<input type="number" class="form-control" id="unit_in_pack" name="unit_in_pack" placeholder="Units in Pack" min="1" >
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Unit type, i.e. Cup, Can, Bottle, Box">Unit Description <span class="text-danger">*</span></label>
									<select id="unit_description" name="unit_description" class="form-control select2" id="unit_description" >
										<option value='' selected> -- Select a value  -- </option>
										<?php if($unitdesc): ?>
											<?php $__currentLoopData = $unitdesc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unitdesclist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($unitdesclist); ?>" <?php if($productdetails->unit_description == $unitdesclist ) echo "selected";?>><?php echo e($unitdesclist); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</select>
									<a href="#" data-toggle="modal" data-target="#new_unit_description_request">New Request</a>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Brand name">Brand <span class="text-danger">*</span></label>
									<select  id="brand" name="brand" class="form-control select2 <?php if(isset($producthistory)){ if($producthistory->brand != $productdetails->brand) echo "edited-field";}?> <?php if(ProductEditPermission('brand') == 0): ?> custom_readonly <?php endif; ?>"  >
										<option value='' selected> -- Select a value  --</option>
										<?php if($brand): ?>
											<?php $__currentLoopData = $brand; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($row_brand); ?>" <?php if($productdetails->brand == $row_brand ) echo "selected";?>><?php echo e($row_brand); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</select>
									<a href="#" data-toggle="modal" data-target="#new_brand_request">New Request</a>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Manufacturer name">Manufacturer <span class="text-danger">*</span></label>
									<select id="manufacturer" name="manufacturer" class="form-control select2 <?php if(isset($producthistory)){ if($producthistory->manufacturer != $productdetails->manufacturer) echo "edited-field";}?> <?php if(ProductEditPermission('manufacturer') == 0): ?> custom_readonly <?php endif; ?>" >
											<option value='' selected> -- Select a value  --</option>
											<option value="<?php echo e($productdetails->manufacturer); ?>" selected><?php echo e($productdetails->manufacturer); ?></option>
									</select>
									<a href="#" data-toggle="modal" data-target="#new_menufectorer_request">New Request</a>
								</div>
							</div>
							<div class="col-md-4">
								<div class="table-responsive card_">
									<table class="table">
										<thead>
											<tr>
												<td colspan="2"><h3 class="card_-title text-center">Product Dimensions Unit</h3></td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Weight  of the item/unit in pounds (lbs.)">
													Weight (lbs) <span class="text-danger">*</span>
												</td>
												<td>
													<input type="number" class="form-control text-center" id="weight" name="weight" placeholder="Weight (lbs)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" value=<?php echo isset($unit_dimensions->weight) ? $unit_dimensions->weight: ''; ?> >
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Length of the item/unit in inches (in)">
													Length (in) <span class="text-danger">*</span>
												</td>
												<td>
													<input type="number" class="form-control text-center" id="length" name="length" placeholder="Length (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" value=<?php echo isset($unit_dimensions->length) ? $unit_dimensions->length: ''; ?>>
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Width (depth)of the item/unit in inches (in)">
													Width (in) <span class="text-danger">*</span>
												</td>
												<td>
													<input type="number" class="form-control text-center" id="width" name="width" placeholder="Width (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" value=<?php echo isset($unit_dimensions->width) ? $unit_dimensions->width: ''; ?> >
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Height of the item/unit in inches (in)">
													Height (in) <span class="text-danger">*</span>
												</td>
												<td>
													<input type="number" class="form-control text-center" id="height" name="height" placeholder="Height (in)"  min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" value=<?php echo isset($unit_dimensions->height) ? $unit_dimensions->height: ''; ?> >
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Individual unit size, i.e. lb. oz.">Unit Size</label>
									<div class="row">
										<div class="col-md-6">
											<input type="number" id="unit_num" name="unit_num" class="form-control" value="<?php echo e($productdetails->unit_num); ?>" placeholder="Select Unit Count" min="0.0001" step="0.0001" pattern="^\d+(?:\.\d{1,4})?$" >
										</div>
										<div class="col-md-6">
											<select id="unit_list" name="unit_list" class="form-control select2" id="unit_list" >
												<option value='' selected> -- Select unit -- </option>
												<?php if($unitsize): ?>
													<?php $__currentLoopData = $unitsize; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unitabbr => $unitname): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
														<option value="<?php echo e($unitabbr); ?>"<?php if($productdetails->unit_list == $unitabbr ) echo "selected";?>><?php echo e($unitname); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												<?php endif; ?>
											</select>
										</div>
									</div>
								</div>

								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="i.e. Case, Pack, Each, Kit">Item Form Description <span class="text-danger">*</span></label>
									<select id="item_form_description" name="item_form_description" class="form-control select2" id="item_form_description" >
											<option value=''> -- Select a value  -- </option>
										<?php if($itemsdesc): ?>
											<?php $__currentLoopData = $itemsdesc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($value); ?>"><?php echo e($value); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</select>
								</div>


							</div>
							<div class="col-md-4">
								<div class="table-responsive card_">
									<table class="table">
										<thead>
											<tr>
												<td colspan="2"><h4 class="card_-title text-center">Product Flags</h4></td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td scope="row" >
													<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Indicates the product contains parts, an ingredient, or is manufactured with chemicals known to cause cancer, birth defects or other reproductive harm defined by the State of California (https://oehha.ca.gov/proposition-65/proposition-65-list)">Prop 65 Flag</label>
												</td>
												<td>
													<div class="form-group col-md-12">
														<select id="prop_65_flag" name="prop_65_flag" class="form-control select2" id="prop_65_flag">
															<option value=''> -- Select a value  -- </option>
															<option value='Yes'  <?php echo e(( $productdetails->prop_65_flag == 'Yes') ? 'selected' : ''); ?>> Yes </option>
															<option value='No'  <?php echo e(( $productdetails->prop_65_flag == 'No') ? 'selected' : ''); ?>> No </option>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<td class="pro_65_container <?php if($productdetails->prop_65_flag != 'Yes' ) echo "ban";?>">
													<div class="form-group">
														<label for="Prop_65_ingredient" data-toggle="tooltip" data-placement="top" title="The Prop 65 ingredient(s)/chemical(s)">Prop 65 Ingredient(s)<br> Not Assigned</label>
														<div class="custom_one_line_cards_container Prop65IngredientDrop border">
															<?php
																$assigned_ingr = explode(',',$productdetails->prop_65_ingredient);

															?>
															<?php if($prop_ingredients): ?>
																<?php $__currentLoopData = $prop_ingredients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$row_prop_ingredients): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

																	<?php if(!in_array($key,$assigned_ingr)): ?>
																		<div class="prop_65_ingredient_cards custom_one_line_cards" id="<?php echo e($key); ?>"><?php echo e($row_prop_ingredients); ?></div>
																	<?php endif; ?>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>
														</div>
													</div>
												</td>
												<td class="pro_65_container <?php if($productdetails->prop_65_flag != 'Yes' ) echo "ban";?>">
													<div class="form-group">
														<input type="hidden" name="prop_65_ingredient" id="Prop_65_ingredient" value="<?php echo e($productdetails->prop_65_ingredient); ?>">
														<label for="Prop_65_ingredient" data-toggle="tooltip" data-placement="top" title="The Prop 65 ingredient(s)/chemical(s)">Prop 65 Ingredient(s) <br>Assigned</label>
														<div class="custom_one_line_cards_container Prop65IngredientDropAssigned border">
															<?php if($prop_ingredients): ?>
																<?php $__currentLoopData = $prop_ingredients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$row_prop_ingredients): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<?php if(in_array($key,$assigned_ingr)): ?>
																		<div class="prop_65_ingredient_cards custom_one_line_cards" id="<?php echo e($key); ?>"><?php echo e($row_prop_ingredients); ?></div>
																	<?php endif; ?>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															<?php endif; ?>

														</div>
													</div>
												</td>
											</tr>
											<tr>
												<td scope="row" >
													<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Indicates the product is hazardous via Yes/No">Hazardous Materials</label>
												</td>
												<td>

														<select class="form-control select2" id="hazardous_materials" name="hazardous_materials" readonly>
															<option value=""> -- Select a value -- </option>
															<option value="Yes" <?php if($productdetails->hazardous_materials == 'Yes'){echo "selected";}?>>Yes</option>
															<option value="No" <?php if($productdetails->hazardous_materials == 'No'){echo "selected";}?>>No</option>
														</select>

												</td>
											</tr>

											<tr>
												<td scope="row" >
													<label for="consignment" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Indicates if the item is on consignment">Consignment Product</label>
												</td>
												<td>

														<select class="form-control select2" id="consignment" name="consignment" readonly>
															<option value="">--Select a value--</option>
															<option value="Yes" <?php if($productdetails->consignment == 'Yes') echo "selected";?>>Yes</option>
															<option value="No" <?php if($productdetails->consignment == 'No') echo "selected";?>>No</option>
														</select>

												</td>
											</tr>

											<tr>
												<td scope="row" >
												<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Indicates the product is a planogram item for hospitality clients">POG Flag</label>
												</td>
												<td>

														<select class="form-control select2" id="POG_flag" name="POG_flag">
															<option value="">--Select a value--</option>
															<option value="Yes" <?php if($productdetails->POG_flag == 'Yes') echo "selected";?>>Yes</option>
															<option value="No" <?php if($productdetails->POG_flag == 'No') echo "selected";?>>No</option>
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
									<input type="text" class="form-control" id="full_product_desc" name="full_product_desc" placeholder="Full Product Descrtiption" value="<?php echo e($productdetails->full_product_desc); ?>" >
								</div>
								<div class="form-group col-md-12">
										<label for="about_this_item" class="ul-form__label <?php if(isset($producthistory)){ if($producthistory->about_this_item != $productdetails->about_this_item) echo "edited-field";}?>" data-toggle="tooltip" data-placement="top" title="Bullet points highlighting the item in Amazon's 'About this item' section">About This Item</label>
										<?php $about_this_item = explode('#',$productdetails->about_this_item);?>
										<input type="text" class="form-control  mb-3 <?php if(ProductEditPermission('about_this_item') == 0): ?> custom_readonly <?php endif; ?>" id="about_this_item_1" name="about_this_item[]" placeholder="Point 1" value="<?php if(isset($about_this_item[0])): ?><?php echo e($about_this_item[0]); ?><?php endif; ?>">
										<input type="text" class="form-control  mb-3 <?php if(ProductEditPermission('about_this_item') == 0): ?> custom_readonly <?php endif; ?>" id="about_this_item_2" name="about_this_item[]" value="<?php if(isset($about_this_item[1])): ?><?php echo e($about_this_item[1]); ?><?php endif; ?>" placeholder="Point 2">
										<input type="text" class="form-control  mb-3 <?php if(ProductEditPermission('about_this_item') == 0): ?> custom_readonly <?php endif; ?>" id="about_this_item_3" name="about_this_item[]" value="<?php if(isset($about_this_item[2])): ?><?php echo e($about_this_item[2]); ?><?php endif; ?>" placeholder="Point 3">
										<input type="text" class="form-control  mb-3 <?php if(ProductEditPermission('about_this_item') == 0): ?> custom_readonly <?php endif; ?>" id="about_this_item_4" name="about_this_item[]" value="<?php if(isset($about_this_item[3])): ?><?php echo e($about_this_item[3]); ?><?php endif; ?>" placeholder="Point 4">
										<input type="text" class="form-control mb-3 <?php if(ProductEditPermission('about_this_item') == 0): ?> custom_readonly <?php endif; ?>" id="about_this_item_5" name="about_this_item[]" value="<?php if(isset($about_this_item[4])): ?><?php echo e($about_this_item[4]); ?><?php endif; ?>" placeholder="Point 5">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Top product category of the hierarchy, i.e. Heat & Serve Meals">Product Category <span class="text-danger">*</span></label>
									<select id="product_category" name="product_category" class="form-control select2 all_product_category <?php if(isset($producthistory)){ if($producthistory->product_category != $productdetails->product_category) echo "edited-field";}?> <?php if(ProductEditPermission('product_category') == 0): ?> custom_readonly <?php endif; ?>" id="product_category"  data-id='0'>
										<option value=''> -- Select a value  -- </option>
										<?php if($categories): ?>
											<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($row_cat->id); ?>" <?php if($productdetails->product_category == $row_cat->id ) echo "selected";?>><?php echo e($row_cat->name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</select>
								</div>


								<div id="next_product_container_0">
									<?php for($i=1; $i<=9;$i++): ?>
										<?php $name = 'product_subcategory'.$i;?>
										<?php if($productdetails->$name != ''): ?>
											<div class="form-group col-md-12">
												<label for="<?php echo e($name); ?>" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="2nd product category of the hierarchy, i.e. Frozen Meals">Product Sub-Category <?php echo e($i); ?></label>
												<select id="<?php echo e($productdetails->$name); ?>" name="<?php echo e($productdetails->$name); ?>" class="form-control select2 <?php if(isset($producthistory)){ if($producthistory->$name != $productdetails->$name) echo "edited-field";}?> <?php if(ProductEditPermission($name) == 0): ?> custom_readonly <?php endif; ?>" id="<?php echo e($name); ?>" >
													<option value="">Select</option>
													<option value='<?php echo e($productdetails->$name); ?>' selected> <?php echo e(CategoryName($productdetails->$name)); ?> </option>
												</select>
											</div>
											<div id="next_product_container_<?php echo e($i); ?>"></div>
										<?php endif; ?>
									<?php endfor; ?>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="i.e. Gluten-Free, Vegetarian, Low-fat">Key Product Attributes & Diet</label>
									<input type="text" class="form-control" id="key_product_attributes_diet" name="key_product_attributes_diet" placeholder="Key Product Attributes & Diet" value="<?php echo e($productdetails->key_product_attributes_diet); ?>">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group col-md-6">
									<label for="product_tags"  data-toggle="tooltip" data-placement="top" title="Product tags/groups for filtering and identification, i.e. Gluten-Free, Vegetarian, Low-fat, good for you, Hospitality-resort, Hospitality-Urban, etc.">Product Tags Not Assigned</label>
									<div class="custom_one_line_cards_container ProductTagsDrop border">
									<?php if($producttag): ?>
										<?php $__currentLoopData = $producttag; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$producttaglist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										  <?php if(!in_array($key,$productdetails->product_tags_array)): ?>
											<div class="product_tags_cards custom_one_line_cards" id="<?php echo e($key); ?>"><?php echo e($producttaglist); ?></div>
											<?php endif; ?>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endif; ?>
									</div>
								</div>
								<div class="form-group col-md-6">
									<input type="hidden" name="product_tags" id="product_tags" value='<?php echo e($productdetails->product_tags); ?>'>
									<label for="product_tags" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Product tags/groups for filtering and identification, i.e. Gluten-Free, Vegetarian, Low-fat, good for you, Hospitality-resort, Hospitality-Urban, etc.">Product Tags Assigned</label>
									<div class="custom_one_line_cards_container ProductTagsDropAssigned border">
									<?php if($producttag): ?>
										<?php $__currentLoopData = $producttag; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$producttaglist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										  <?php if(in_array($key,$productdetails->product_tags_array)): ?>
											<div class="product_tags_cards custom_one_line_cards" id="<?php echo e($key); ?>"><?php echo e($producttaglist); ?></div>
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
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label">Supplier Description</label>
									<input type="text" class="form-control" id="supplier_description" name="supplier_description" placeholder="Supplier Description" value="<?php echo e($productdetails->supplier_description); ?>" readonly>
								</div>
								<div class="form-group col-md-12">									
									<div class="row">
										<div class="col-md-4">
											<label for="supplier_type" class="ul-form__label">Supplier Type<span class="text-danger">*</span></label>
										</div>										
										<div class="col-md-4">
											<input type="radio" id="type_supplier" name="sup_type" value="type_supplier" onchange="changeSuppliers('supplier')" <?php if($productdetails->supplier_type == 'supplier' ) echo "checked";?>/>
											<label for="type_sup" class="ul-form__label">Supplier</label>
										</div>
										<div class="col-md-4">
											<input type="radio" id="type_client" name="sup_type" value="type_client" onchange="changeSuppliers('client')" <?php if($productdetails->supplier_type == 'client' ) echo "checked";?>/>
											<label for="type_cl" class="ul-form__label">Client</label>
										</div>
									</div>
								</div>
								<div class="table-responsive  col-md-12">
									<table class="table table-hover">
										<tbody>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Current Supplier Name">
													Current Supplier <span class="text-danger">*</span>
												</td>
												<td>
													<select id="current_supplier" name="current_supplier" class="form-control select2 <?php if(isset($producthistory)){ if($producthistory->current_supplier != $productdetails->current_supplier) echo "edited-field";}?> <?php if(ProductEditPermission('current_supplier') == 0): ?> custom_readonly <?php endif; ?>" id="current_supplier" >
															<option value=''> -- Select a value  -- </option>
														<?php if($productdetails->supplier_type === 'supplier' && $supplier): ?>
															<?php $__currentLoopData = $supplier; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($key); ?>" <?php if($productdetails->client_supplier_id == $key ) echo "selected";?>><?php echo e($value); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														<?php elseif($productdetails->supplier_type === 'client' && $client): ?>
															<?php $__currentLoopData = $client; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($key); ?>" <?php if($productdetails->client_supplier_id == $key ) echo "selected";?>><?php echo e($value); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														<?php endif; ?>
													</select>
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Current Supplier's Status/Availability, i.e. Backorder, Special Order">
													Supplier Status
												</td>
												<td>
													<?php if(count($supplier_status) == 0): ?>
														<input type="text" class="form-control <?php if(isset($producthistory)){ if($producthistory->current_supplier != $productdetails->current_supplier) echo "edited-field";}?> <?php if(ProductEditPermission('current_supplier') == 0): ?> custom_readonly <?php endif; ?>" id="supplier_status" name="supplier_status" placeholder="Supplier Status">
														<?php else: ?>
														<select class="form-control select2 <?php if(isset($producthistory)){ if($producthistory->supplier_status != $productdetails->supplier_status) echo "edited-field";}?> <?php if(ProductEditPermission('supplier_status') == 0): ?> custom_readonly <?php endif; ?>" id="supplier_status" name="supplier_status" placeholder="Supplier Status">
															<option value=''> -- Select a value  -- </option>
															<?php $__currentLoopData = $supplier_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($row->id); ?>"  <?php if($productdetails->supplier_status == $row->id ) echo "selected";?>><?php echo e($row->supplier_status); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
													<?php endif; ?>
												</td>
											</tr>
											<tr>
												<td scope="row">Alternate Supplier(s)</td>
												<td>N.A.</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Notates the supplier's availability for dropship">
													Dropship Available
												</td>
												<td>
													<select class="form-control select2" id="dropship_available" name="dropship_available">
														<option value="">--Select a value--</option>
														<option value="Yes" <?php if($productdetails->dropship_available == 'Yes') echo "selected";?>>Yes</option>
														<option value="No" <?php if($productdetails->dropship_available == 'No') echo "selected";?>>No</option>
													</select>
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Product cost from supplier: (Case Cost / Case Count) x Pack Form">
													Cost <span class="text-danger">*</span>
												</td>
												<td><input type="number" class="form-control"id="cost" name="cost" placeholder="Cost" value="<?php echo e($productdetails->cost); ?>" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$" ></td>
											</tr>
                                            <tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Acquisition Cost">
													Acquisition Cost
												</td>
												<td><input type="number" class="form-control"id="acquisition_cost" name="acquisition_cost" placeholder="Acquisition Cost" value="<?php echo e($productdetails->acquisition_cost); ?>" min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$" ></td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="New Product cost from supplier">
													New Cost
												</td>
												<td><input type="number" class="form-control" id="new_cost" name="new_cost" placeholder="New Cost" value="<?php echo e($productdetails->new_cost); ?>"  min="0.01" step="0.01" pattern="^\d+(?:\.\d{1,2})?$"></td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Date the new cost goes in effect">
													New Cost Date
												</td>
												<td><input type="date" class="form-control" id="new_cost_date" name="new_cost_date" placeholder="New Cost Date" value="<?php echo e($productdetails->new_cost_date); ?>"></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="col-md-4">
								<div class="table-responsive card_">
									<table class="table table-hover">
										<thead>
											<tr>
												<td colspan="2"><h3 class="card_-title text-center">Product Codes Unit</h3></td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Supplier's product number used for purchasing">Supplier Product Number</td>
												<td>
													<input type="text" class="form-control text-center" id="supplier_product_number" name="supplier_product_number" placeholder="Supplier Product Number">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Manufacturer's product number">Manufacturer Product Number</td>
												<td><input type="text" class="form-control" id="manufacture_product_number" name="manufacture_product_number" placeholder="Manufacturer Product Number"></td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="12 Digit UPC (Universal Product Code) of the item/unit being sold">
													UPC <span class="text-danger">*</span>
													<input type="checkbox" id="upc_present" name="upc_present" onchange="disableEnableTextBox(this, 'upc','upc_scanable')" checked=true class="float-right">
												</td>
												<td>
													<input type="text" class="form-control text-center" id="upc" name="upc" placeholder="UPC" minlength="12" maxlength="12" value="<?php echo isset($unit_dimensions->upc) ? $unit_dimensions->upc: ''; ?>" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='validatetext(event)'>
												</td>
											</tr>
											<tr>
												<td class="border-0" scope="row" data-toggle="tooltip" data-placement="top" title="UPC Scanable">Scanable
													<input type="checkbox" id="upc_scanable" name="upc_scanable" checked class="float-right" />
												</td>
												<td class="border-0">
													
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="14 Digit case GTIN (Global Trade Identification Number) of the item/unit being sold">GTIN <span class="text-danger">*</span>
												<input type="checkbox" id="gtin_present" name="gtin_present" onchange="disableEnableTextBox(this, 'gtin','gtin_scanable')" checked=true class="float-right">
											</td>
												<td>
													<input type="text" class="form-control text-center" id="gtin" name="gtin" placeholder="GTIN" minlength="14" maxlength="14" value="<?php echo isset($unit_dimensions->gtin) ? $unit_dimensions->gtin: ''; ?>"oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" onkeypress='validatetext(event)'>
												</td>
											</tr>
											<tr>
												<td class="border-0" scope="row" data-toggle="tooltip" data-placement="top" title="GTIN Scanable">
													Scanable 
													<input type="checkbox" id="gtin_scanable" name="gtin_scanable" checked  class="float-right"/></td>
												<td class="border-0">
													
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Amazon Standard Identification Number">ASIN</td>
												<td>
													<input type="text" class="form-control text-center" id="asin" name="asin" placeholder="ASIN">
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Global Product Classification Code for international shipping etc.">GPC Code</td>
												<td>
													<input type="text" class="form-control text-center" id="GPC_code" name="GPC_code" placeholder="GPC Code" value="<?php echo e($productdetails->GPC_code); ?>" readonly>
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Global Product Classification Category for international shipping etc.">GPC Class</td>
												<td>
													<input type="text" class="form-control text-center" id="GPC_class" name="GPC_class" placeholder="GPC Class" value="<?php echo e($productdetails->GPC_class); ?>" readonly>
												</td>
											</tr>
											<tr>
												<td scope="row" data-toggle="tooltip" data-placement="top" title="Harmonized System for International shipping">HS Code</td>
												<td>
													<input type="text" class="form-control text-center" id="HS_code" name="HS_code" placeholder="HS Code" value="<?php echo e($productdetails->HS_code); ?>" readonly>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

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

					<div class="tab-pane fade " id="tab_manufacturer" role="tabpanel" area-labelledby="manufacturer_tab">
						<div class="row mt-4">
							<div class="col-md-4">
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Number of days between manufacturing & expiration">MFG Shelf Life (In days)</label>
									<input type="text" class="form-control" id="MFG_shelf_life" name="MFG_shelf_life" placeholder="MFG Shelf Life" value="<?php echo e($productdetails->MFG_shelf_life); ?>" min="1" max="99999" readonly>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Country where the product is produced, manufactured, or grown">Country of Origin</label>
									<select id="country_of_origin" name="country_of_origin" class="form-control select2" id="country_of_origin">
											<option value=''> -- Select a value  -- </option>
											<?php if($country): ?>
												<?php $__currentLoopData = $country; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$countrylist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($key); ?>" <?php if($productdetails->country_of_origin == $key ) echo "selected";?>><?php echo e($countrylist); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
									</select>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="List of all ingredients">Ingredients</label>
									<input type="text" class="form-control" id="ingredients" name="ingredients" placeholder="Ingredients" value="<?php echo e($productdetails->ingredients); ?>" readonly>
								</div>
							</div>
							<div class="col-md-4">
								<!-- <div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="List of known allergens">Allergens</label>
									<input type="text" class="form-control" id="allergens" name="allergens" placeholder="Allergens" value="<?php echo e($productdetails->allergens); ?>" readonly>
								</div> -->
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Storage Description, i.e. The temperature and humidity ranges are designed to protect the quality attributes of the products. Products should be stored at a temperature of 70F +/- 5F, humidity of 50% +/- 10% Relative Humidity">Storage</label>
									<input type="text" class="form-control" id="storage" name="storage" placeholder="Storage" value="<?php echo e($productdetails->storage); ?>" readonly>
								</div>
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Description of the shipped packaging, i.e. Item is shipped inside of a cooler with dry ice and an outer box">Package Information</label>
									<input type="text" class="form-control" id="package_information" name="package_information" placeholder="Package Information" value="<?php echo e($productdetails->package_information); ?>">
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group col-lg-6">
									<label for="allergens"  data-toggle="tooltip" data-placement="top" title="List of known allergens">Allergens Not Assigned</label>
									<div class="custom_one_line_cards_container AllergensDrop border">
										<?php if($allergens): ?>
											<?php $__currentLoopData = $allergens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$row_allergens): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if(!in_array($key,$productdetails->allergens_array)): ?>
													<div class="allergens_cards custom_one_line_cards" id="<?php echo e($key); ?>"><?php echo e($row_allergens); ?></div>
												<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</div>
								</div>
								<div class="form-group col-lg-6">
									<input type="hidden" name="allergens" id="allergens" value="<?php echo e($productdetails->allergens); ?>">
									<label for="allergens" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="List of known allergens">Allergens Assigned <span class="text-danger">*</span></label>
									<div class="custom_one_line_cards_container AllergensDropAssigned border">
										<?php if($allergens): ?>
											<?php $__currentLoopData = $allergens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$row_allergens): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if(in_array($key,$productdetails->allergens_array)): ?>
													<div class="allergens_cards custom_one_line_cards" id="<?php echo e($key); ?>"><?php echo e($row_allergens); ?></div>
												<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<div class="col-md-4">

							</div>
						</div>
					</div>



					<div class="tab-pane fade " id="tab_history" role="tabpanel" area-labelledby="history_tab">
						<div class="row">
							<div class="form-group col-md-12">
							<br>
								<h3 class="card_-title text-center">Product History</h3>
								<input type="text" class="form-control text-center" id="duplicate_full_product_desc" name="duplicate_full_product_desc" placeholder="Product History">
							</div>
						</div>
					</div>

					<div class="tab-pane fade " id="tab_misc" role="tabpanel" area-labelledby="misc_tab">
						<div class="row">
							<div class="form-group col-md-4">
								<label for="total_ounces" class="ul-form__label">Total Ounces <small>(Auto Generated)</small></label>
								<input type="text" class="form-control" id="total_ounces" name="total_ounces" placeholder="Total Ounces"  value="<?php echo e($productdetails->total_ounces); ?>" readonly>
							</div>
						</div>
					</div>
					<div class="tab-pane fade " id="tab_clients" role="tabpanel" area-labelledby="clients_tab">
						<div class="row">
							<div class="form-group col-md-6">
								<div class="form-group col-md-12">
									<label for="inputEmail4" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="*Notes where 3PL products on consignment can be sold
									*Manufacturer, supplier or misc. restrictions where a product can or cannot be sold. i.e. Blocked from all, blocked from AMZ">Channel Listing Restrictions</label>
									<input type="text" class="form-control <?php if(isset($producthistory)){ if($producthistory->channel_listing_restrictions != $productdetails->channel_listing_restrictions) echo "edited-field";}?>" id="channel_listing_restrictions" name="channel_listing_restrictions" placeholder="Channel Listing Restrictions" value="<?php echo e($productdetails->channel_listing_restrictions); ?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								<div class="form-group col-md-6">
									<label for="lobs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Clients & Sites Not Assigned</label>
									<div class="custom_one_line_cards_container LobsDrop  border">
										<?php  $lobs = explode(',',$productdetails->lobs); ?>
										<?php if($client): ?>
											<?php $__currentLoopData = $client; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$clients): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if(!in_array($key,$lobs)): ?>
													<div class="lobs_cards custom_one_line_cards" id="<?php echo e($key); ?>"><?php echo e($clients); ?></div>
												<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</div>
								</div>
								<div class="form-group col-md-6">
									<input type="hidden" name="lobs" id="lobs" value="<?php echo e($productdetails->lobs); ?>">
									<label for="lobs" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Clients & sites the product is assigned to">Clients & Sites Assigned <span class="text-danger">*</span></label>
									<div class="custom_one_line_cards_container LobsDropAssigned border">
										<?php if($client): ?>
											<?php $__currentLoopData = $client; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$clients): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<?php if(in_array($key,$lobs)): ?>
													<div class="lobs_cards custom_one_line_cards" id="<?php echo e($key); ?>"><?php echo e($clients); ?></div>
												<?php endif; ?>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<div class="form-group col-md-6">
								<input type="hidden" name="chanel_ids" id="chanel_ids" value="<?php echo e($productdetails->chanel_ids); ?>">
								<div  id="client_channel_container"></div>
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
						<button class="btn  btn-outline-success m-1 submit" id="saveAsDraft">Save as Draft</button>
						<button type="submit" class="btn btn-primary m-1">Save</button>
						<a href="<?php echo e(route('allmasterproductlsts')); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<div id="new_menufectorer_request" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header-->
			<div class="modal-header" style="background-color:#fff;">
				<h3>New Manufacturer Request</h3>
				<!--Close/Cross Button-->
				<button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
			</div>
			<form  method="POST" action="javascript:void(0)" id="add_new_menufectorer_request_form" >
				<?php echo csrf_field(); ?>
				<div class="modal-body">
					<label>Manufacturer Name</label>
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
<div id="new_brand_request" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header-->
			<div class="modal-header" style="background-color:#fff;">
				<h3>New Brand Request</h3>
				<!--Close/Cross Button-->
				<button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
			</div>
			<form  method="POST" action="javascript:void(0)" id="add_new_brand_request_form" >
				<?php echo csrf_field(); ?>
				<div class="modal-body">
					<label>Brand Name</label>
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
<div id="new_unit_description_request" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header-->
			<div class="modal-header" style="background-color:#fff;">
				<h3>New Unit Description Request</h3>
				<!--Close/Cross Button-->
				<button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
			</div>
			<form  method="POST" action="javascript:void(0)" id="add_new_unit_description_request_form" >
				<?php echo csrf_field(); ?>
				<div class="modal-body">
					<label>Unit Description</label>
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

function disableEnableTextBox(value, id, scanable_id){
	if (!value.checked) {
		$("#" + id).attr("disabled", "disabled");	
		$("#" + id).val("");
		$("#" + scanable_id).prop("checked", false);
		$("#" + scanable_id).attr("disabled", "disabled");	
	} else {
		$("#" + id).removeAttr("disabled");
		$("#" + scanable_id).prop("checked", true);
		$("#" + scanable_id).attr("disabled", false);	
	}
}

// Brand Dropdown
$('#brand').change(function () {
	 var name = $(this).val();
	 var myurl1 = "<?php echo e(url('getmanufacturer')); ?>" +"/"+ name;
	 var token = "<?php echo e(csrf_token()); ?>";
	 $('#manufacturer').html('');
	 $.ajax({
		url:myurl1,
		type:'GET',
		dataType:'json',
		data: {
			'_token': token,
			'_method': 'GET',
			'name' : name
			},
		success:function (response) {
			//alert(response);
			var len = 0;
			if (response.data != null) {
				len = response.data.length;
			}

			if (len>0) {
				for (var i = 0; i<len; i++) {
					 var name = response.data[i].manufacturer_name;
					 var option = '<option value="'+name+'">'+name+'</option>';
					 if(name != null )
						{
							$("#manufacturer").append(option);
						}

				}
			}
		}
	})
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



// Categoty - Subcategory dropdown

$('#product_category').change(function () {
	 var id = $(this).val();
	 var myurl = "<?php echo e(url('getsubcategories1')); ?>" +"/"+ id;
	 var token = "<?php echo e(csrf_token()); ?>";
	 $('#product_subcategory1').find('option').not(':first').remove();
	 $('#product_subcategory2').find('option').not(':first').remove();
	 $('#product_subcategory3').find('option').not(':first').remove();
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
			//alert(response);
			var len = 0;
			if (response.data != null) {
				len = response.data.length;
			}

			if (len>0) {
				for (var i = 0; i<len; i++) {
					 //var id = response.data[i].id;
					 var name = response.data[i].sub_category_1;
					 var option = "<option value='"+name+"'>"+name+"</option>";
					 if(name != null )
						{
							$("#product_subcategory1").append(option);
						}

				}
			}
		}
	})
});

$('#product_subcategory1').change(function () {
	 var sub1 = $("#product_subcategory1 option:selected").text();
	 var myurl = "<?php echo e(url('getsubcategories2')); ?>" +"/"+ sub1;
	 var token = "<?php echo e(csrf_token()); ?>";
	 $('#product_subcategory2').find('option').not(':first').remove();
	 $('#product_subcategory3').find('option').not(':first').remove();
	 $.ajax({
		url:myurl,
		type:'GET',
		dataType:'json',
		data: {
			'_token': token,
			'_method': 'GET',
			'id' : sub1
			},
		success:function (response) {
			//alert(sucess);
			var len = 0;
			if (response.data != null) {
				len = response.data.length;
			}

			if (len>0) {
				for (var i = 0; i<len; i++) {
					 //var id = response.data[i].id;
					 var name = response.data[i].sub_category_2;
					 var option = "<option value='"+name+"'>"+name+"</option>";
					 if(name != null )
						{
							$("#product_subcategory2").append(option);
						}

				}
			}
		}
	})
});
$('#product_subcategory2').change(function () {
	 var sub2 = $("#product_subcategory2 option:selected").text();
	 var myurl = "<?php echo e(url('getsubcategories3')); ?>" +"/"+ sub2;
	 var token = "<?php echo e(csrf_token()); ?>";
	 $('#product_subcategory3').find('option').not(':first').remove();
	 $.ajax({
		url:myurl,
		type:'GET',
		dataType:'json',
		data: {
			'_token': token,
			'_method': 'GET',
			'id' : sub2
			},
		success:function (response) {
			//alert(sucess);
			var len = 0;
			if (response.data != null) {
				len = response.data.length;
			}

			if (len>0) {
				for (var i = 0; i<len; i++) {
					// var id = response.data[i].id;
					 var name = response.data[i].sub_category_3;
					 var option = "<option value='"+name+"'>"+name+"</option>";
					 if(name != null )
						{
							$("#product_subcategory3").append(option);
						}

				}
			}
		}
	})
});


$('#prop_65_flag').change(function () {
	var propflag = $(this).val();
	if(propflag === 'Yes'){
		$('.pro_65_container').removeClass('ban');
	} else {
		$('.pro_65_container').addClass('ban');
	}
});


// Auto Populate Product Listing Name
$("#brand").on("input", function() {
	inputproductlisting()
});
$("#flavor").on("input", function() {
	inputproductlisting()
});
$("#product_type").on("input", function() {
	inputproductlisting()
});
$("#unit_num").on("input", function() {
	inputproductlisting()
});
$("#unit_list").on("input", function() {
	inputproductlisting()
});
$("#unit_description").on("input", function() {
	inputproductlisting()
});
$("#pack_form_count").on("input", function() {
	inputproductlisting()
});
$("#unit_in_pack").on("input", function() {
	inputproductlisting()
});
$("#item_form_description").on("change", function() {
	inputproductlisting()
});
function inputproductlisting(){
   var brand = $("#brand").val();
   var flavor = $("#flavor").val();
   var product_type = $("#product_type").val();
   var unit_num = $("#unit_num").val();
   var unit_list = $("#unit_list").val();
   var unit_description = $("#unit_description").val();
   var pack_form_count = $("#pack_form_count").val();
   var unit_in_pack = $("#unit_in_pack").val();
   var item_form_description = $("#item_form_description").val();


   var productlisting = brand+' '+flavor+' '+product_type+', '+unit_num+' '+unit_list+' ' +unit_description+' ('+pack_form_count+'-'+unit_in_pack+' '+ item_form_description+')';
   $("#product_listing_name").val();
   $("#product_listing_name").val(productlisting);
}

// Auto Populate Total Ounces
$("#unit_num").on("input", function() {
	inputtotalounces()
});
$("#pack_form_count").on("input", function() {
	inputtotalounces()
});
function inputtotalounces(){
	var unit_num = $("#unit_num").val();
	var pack_form_count = $("#pack_form_count").val();
	var totalounces = unit_num * pack_form_count;
	$("#total_ounces").val();
	$("#total_ounces").val(totalounces);
  }



</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('bottom-js'); ?>
<script>
	$(".warehouse_cards").draggable({
		appendTo: "body",
		cursor: "move",
		helper: "clone",
		revert: "invalid",
	});

	$(".warehouseDrop").droppable({
		tolerance: "intersect",
		accept: ".warehouse_cards",
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		drop: function (event, ui) {
			var dropped_warehouse = ui.draggable.attr('id');
			var warehouses_assigned = $("#warehouses_assigned").val();
			var warehouse_array = [];
			if(warehouses_assigned == ''){

			}else{
				warehouse_array = warehouses_assigned.split(',');
				warehouse_array.splice($.inArray(dropped_warehouse, warehouse_array), 1);
				$("#warehouses_assigned").val(warehouse_array.join(','));
			}
			$(this).append($(ui.draggable));
		},
	});

	$(".warehouseDropAssigned").droppable({
		tolerance: "intersect",
		accept: ".warehouse_cards",
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		drop: function (event, ui) {
			var dropped_warehouse = ui.draggable.attr('id');
			var warehouses_assigned = $("#warehouses_assigned").val();
			var warehouse_array = [];
			if(warehouses_assigned == ''){
				warehouse_array.push(dropped_warehouse);
				$("#warehouses_assigned").val(warehouse_array.join(','));
			}else{
				warehouse_array = warehouses_assigned.split(',');
				warehouse_array.push(dropped_warehouse);
				$("#warehouses_assigned").val(warehouse_array.join(','));
			}
			$(this).append($(ui.draggable));
		},
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
		GetClientChanel();
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
		GetClientChanel();
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

	$('.Prop65IngredientDrop ').on('click','.prop_65_ingredient_cards',function(e){
		var dropped_Prop_65_ingredient = $(this).attr('id');
		console.log(dropped_Prop_65_ingredient);
		var Prop_65_ingredient_assigned = $("#Prop_65_ingredient").val();
		var Prop_65_ingredient_array = [];
		if(Prop_65_ingredient_assigned == ''){
			Prop_65_ingredient_array.push(dropped_Prop_65_ingredient);
			$("#Prop_65_ingredient").val(Prop_65_ingredient_array.join(','));
		}else{
			Prop_65_ingredient_array = Prop_65_ingredient_assigned.split(',');
			Prop_65_ingredient_array.push(dropped_Prop_65_ingredient);
			$("#Prop_65_ingredient").val(Prop_65_ingredient_array.join(','));
		}

		$('.Prop65IngredientDropAssigned').append(this);
	});

	$(".Prop65IngredientDropAssigned").on('click','.prop_65_ingredient_cards',function(e){
		var dropped_Prop_65_ingredient = $(this).attr('id');
		console.log(dropped_Prop_65_ingredient);
		var Prop_65_ingredient_assigned = $("#Prop_65_ingredient").val();
		var Prop_65_ingredient_array = [];
		if(Prop_65_ingredient_assigned == ''){

		}else{
			Prop_65_ingredient_array = Prop_65_ingredient_assigned.split(',');
			Prop_65_ingredient_array.splice($.inArray(dropped_Prop_65_ingredient, Prop_65_ingredient_array), 1);
			$("#Prop_65_ingredient").val(Prop_65_ingredient_array.join(','));
		}
		$('.Prop65IngredientDrop').append(this);
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


</script>

<script>
$("#product_add").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
        $('div#preloader').show();
		var form_cust = $('#product_add')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '<?php echo e(route('addchildproduct')); ?>',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
                
				if(response.error == 0){
					toastr.success(response.msg);
					location.reload();
					// window.location.href = response.url
				}else{
					$(".submit").attr("disabled", false);
					toastr.error(response.msg);
					$('div#preloader').hide();
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

//Save as Draft0

$('#saveAsDraft').click(function(e){
	$(".submit").attr("disabled", true);
	$('input').attr('required', false);
    $('div#preloader').show();
	// $("#product_add").submit(function(e) {
		e.preventDefault();
		var form_cust = $('#product_add')[0];
		let form1 = new FormData(form_cust);
		// var form = $('#product_add');
		var url = '/saveAsDraftChild';
		console.log(form1);
		$.ajax({
			type: "POST",
			url: url,
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
                // $('div#preloader').hide();
				if(response.error == 0){
					toastr.success(response.msg);
					// window.location.href = response.url
					location.reload();
					
				}else{
					$(".submit").attr("disabled", false);
					toastr.error(response.msg);
					$('div#preloader').hide();
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
		});
	// });
});

$("#add_new_brand_request_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        $('div#preloader').show();
        var form_cust = $('#add_new_brand_request_form')[0];
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '<?php echo e(route('new_brand_request')); ?>',
            data: form1,
            processData: false,
            contentType: false,
            success: function( response ) {
                $('div#preloader').hide();
                if(response.error == 0){
					$(".submit").attr("disabled", false);
                    toastr.success(response.msg);
					$("#new_brand_request").modal('hide');
                }else{
                    $(".submit").attr("disabled", false);
                    toastr.error(response.msg);
                }
            },
            error: function(data){
                $(".submit").attr("disabled", false);
                $('div#preloader').hide();
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

$("#add_new_menufectorer_request_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        $('div#preloader').show();
        var form_cust = $('#add_new_menufectorer_request_form')[0];
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '<?php echo e(route('new_manufacturers_request')); ?>',
            data: form1,
            processData: false,
            contentType: false,
            success: function( response ) {
                $('div#preloader').hide();
                if(response.error == 0){
					$(".submit").attr("disabled", false);
                    toastr.success(response.msg);
					$("#new_menufectorer_request").modal('hide');
                }else{
                    $(".submit").attr("disabled", false);
                    toastr.error(response.msg);
                }
            },
            error: function(data){
                $(".submit").attr("disabled", false);
                $('div#preloader').hide();
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

$("#add_new_product_type_request_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        $('div#preloader').show();
        var form_cust = $('#add_new_product_type_request_form')[0];
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '<?php echo e(route('new_product_type_request')); ?>',
            data: form1,
            processData: false,
            contentType: false,
            success: function( response ) {
                $('div#preloader').hide();
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
                $('div#preloader').hide();
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


$("#add_new_unit_description_request_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        $('div#preloader').show();
        var form_cust = $('#add_new_unit_description_request_form')[0];
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '<?php echo e(route('new_unit_description_request')); ?>',
            data: form1,
            processData: false,
            contentType: false,
            success: function( response ) {
                $('div#preloader').hide();
                if(response.error == 0){
					$(".submit").attr("disabled", false);
                    toastr.success(response.msg);
					$("#new_unit_description_request").modal('hide');
                }else{
                    $(".submit").attr("disabled", false);
                    toastr.error(response.msg);
                }
            },
            error: function(data){
                $(".submit").attr("disabled", false);
                $('div#preloader').hide();
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

var i = 1;
function AddRow(){
	// alert(i);
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

function validatetext(evt) {
	var theEvent = evt || window.event;

	// Handle paste
	if (theEvent.type === 'paste') {
		key = event.clipboardData.getData('text/plain');
	} else {
	// Handle key press
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode(key);
	}
	var regex = /[0-9]|\./;
	if( !regex.test(key) ) {
		theEvent.returnValue = false;
		if(theEvent.preventDefault) theEvent.preventDefault();
	}
}

function changeSuppliers(type) {
	var toAppend = 'Hello'
	if (type === 'client') {
		toAppend = <?php echo json_encode($client, 15, 512) ?>;
	} else if (type === 'supplier') {
		toAppend = <?php echo json_encode($supplier, 15, 512) ?>
	}
	
	var select_elem = document.getElementById('current_supplier');
	var options = select_elem.getElementsByTagName('option');
	for (var i = options.length; i--;) {	
		select_elem.removeChild(options[i]);
	}
	
	let opt = document.createElement("option");
	opt.value = ''; 
	opt.innerHTML = '-- Select a value  --'; 
	select_elem.append(opt);
	
	for (var key in toAppend) {
		let opt = document.createElement("option");
		opt.value = key; 
		opt.innerHTML = toAppend[key]; 
		select_elem.append(opt); 
	}
}

function GetClientChanel(){
	$.ajax({
		method:'POST',
		url:'<?php echo e(route('getClientChanels')); ?>',
		data:{lobs:$("#lobs").val(),chanel_ids:$("#chanel_ids").val()},
		dataType:'html',
		success:function(res){
			$("#client_channel_container").html(res);
		}
	})
}

$(function(){
	GetClientChanel();
})

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/cranium/supplierProdListing/childproductadd.blade.php ENDPATH**/ ?>