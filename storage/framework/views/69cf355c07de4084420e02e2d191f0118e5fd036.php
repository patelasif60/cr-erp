

<?php $__env->startSection('page-css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('assets/custom/css/custom.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/sweetalert2.min.css')); ?>">
<link rel="stylesheet" href="https://phpcoder.tech/multiselect/css/jquery.multiselect.css">
<style>
	.ms-options > ul{
		list-style-type: none !important;
	}
	.ms-options-wrap > .ms-options{
		position: relative;
	}
	.table-responsive .dropdown-menu{
		/* position: relative; */
		min-width:300px;
		z-index: 950 !important;
		padding:10px;
	}
	.filter-input-text{
		width:200%;
	}
	.dropdown-menu.show1 {
		display: block;
	}

	/* .dataTables_scrollHead{
		overflow: inherit !important;
	} */


</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<?php if(session('approved')): ?>
	<div class="alert alert-success" role="alert">
		<?php echo e(session('approved')); ?>

	</div>
<?php endif; ?>
<?php if(session('not_approved')): ?>
	<div class="alert alert-danger" role="alert">
		<?php echo e(session('not_approved')); ?>

	</div>
<?php endif; ?>
<div class="breadcrumb">
	<h1>Cranium</h1>
	<ul>
		<li><a href="javascript:void(0);">All Master Product Listing</a></li>
		<li>Table View</li>
	</ul>
</div>
<div class="separator-breadcrumb border-top">
	<?php if(ReadWriteAccess('AddNewParentProduct')): ?>
        <a href="<?php echo e(route('addnewmasterproductview')); ?>" class="btn btn-primary btn-icon m-1" style=" float: right;">
            <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Add New Parent Product
        </a>
    <?php endif; ?>
        <a href="<?php echo e(route('kits.create')); ?>" class="btn btn-primary btn-icon m-1" style=" float: right;">
            <img src="<?php echo e(asset('assets/images/addnew.png')); ?>" style="width: 15px; cursor: pointer;">&nbsp; Add New Kit
        </a>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card o-hidden mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col-md-2">
						<h3 class="w-50 float-left card-title m-0">Filters</h3>
					</div>
				</div>
			</div>
			<div class="card-body">

				<div class="mb-3 row" style="">
					<div class="col-md-9">
						<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modelFilters">
							Show / Hide Filters
						</button> -->
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modelColumns" data-backdrop="static" data-keyboard="false">
							Show / Hide Columns
						</button>
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modelSmartFilters" data-backdrop="static" data-keyboard="false">
							Smart Filters
						</button>
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modelBulkActions" data-backdrop="static" data-keyboard="false">
							Bulk Actions
						</button>



						<button type="button" class="btn btn-primary" id="btn_open_save_as_modal" data-toggle="modal" data-target="#modelSaveASSmartFilter" style="display:none">
							Save As
						</button>

						<?php if($id != NULL): ?>
						<button type="button" class="btn btn-primary" id="btn_save_smart_filter" style="display:none">
							Save
						</button>
						<a href="<?php echo e(url('/masterparoducts_approved')); ?>" class="btn btn-warning">Clear Filters</a>
						<?php endif; ?>
					</div>
					<div class="col-md-3">
						<?php if($id != NULL): ?>
						<b>Current Filter :</b> <?php echo $smart_filter->filter_name?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card o-hidden mb-4">
			<div class="card-header">
				<div class="row">
					<div class="col-md-4">
						<h3 class="w-50 float-left card-title m-0">Active Product Listings</h3>
					</div>
					<!-- <div class="col-md-2">
					</div> -->
					<div class="col-md-2">

					</div>
					<div class="col-md-2">
					</div>
					<div class="col-md-2">
					</div>
					<div class="col-md-2">
						<a onclick="refreshdatatable()"><img src="<?php echo e(asset('assets/images/refresh.png')); ?>" style="width: 25px; float: right; cursor: pointer;"></a>
					</div>
				</div>
			</div>

			<div class="card-body">
				<div class="table-responsive" style="min-height:700px">
					<form action="javascript:void(0);" id="form_filters" method="POST">
						<?php echo csrf_field(); ?>
						<?php if($product_listing_filter): ?>
							<?php $__currentLoopData = $product_listing_filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row_product_listing_filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php $display = false;?>
								<?php if($id != NULL && !in_array($row_product_listing_filter->sorting_order,$hidden_cols_arr)): ?>
									<?php $display = true;?>
								<?php endif; ?>
								<?php if($id == NULL && $row_product_listing_filter->is_default == 1): ?>
									<?php $display = true;?>
								<?php endif; ?>
								<div class="dropdown mb-2 fl_<?php echo e($row_product_listing_filter->sorting_order); ?>" style="<?php if($display == true) echo "display:inline-block !important"; else echo "display:none"?>">
									<a class="btn btn-secondary dropdown-toggle dropdown-filter" href="#" role="button" id="dropdownMenuLink<?php echo e($row_product_listing_filter->id); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
										<?php echo e($row_product_listing_filter->label_name); ?>

									</a>

									<div class="dropdown-menu" aria-labelledby="dropdownMenuLink" >
										<div id="filter_dropdown_<?php echo $row_product_listing_filter->id; ?>">
										<?php
											$selected_value = '';
											if(isset($main_filter[$row_product_listing_filter->column_name])){
												$selected_value = $main_filter[$row_product_listing_filter->column_name];
											}
										?>
										<input type="hidden" name="main_filter[<?php echo e($row_product_listing_filter->column_name); ?>]" id="main_filter_<?php echo e($row_product_listing_filter->column_name); ?>" value="<?php echo e($selected_value); ?>">
										<input type="hidden" class="filter-input-text"  name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][info]" value="<?php echo htmlspecialchars(json_encode($row_product_listing_filter))?>">
										<?php if($row_product_listing_filter->text_or_select == "Text"): ?>

										<?php elseif($row_product_listing_filter->text_or_select == 'custom_select'): ?>
										<?php $hidden_all = true;?>
										<select name="boolean_filters[<?php echo e($row_product_listing_filter->column_name); ?>]" class="form-control" id="drop_down_<?php echo e($row_product_listing_filter->column_name); ?>">
												<option value="">Please Select</option>
												<?php
													$all_select_options = explode(',',$row_product_listing_filter->custom_select_options);
													foreach($all_select_options as $option){
														$key_value = explode(':',$option);
														$count = count($key_value);
														if($count > 1){
															echo '<option value="'.$key_value[0].'">'.$key_value[1].'</option>';
														}else{
															echo '<option value="'.$key_value[0].'">'.$key_value[0].'</option>';
														}
													}
												?>
											</select>
										<?php else: ?>
											<select name="<?php echo e($row_product_listing_filter->column_name); ?>" class="form-control select2" id="drop_down_<?php echo e($row_product_listing_filter->column_name); ?>" onchange="GetSelectedValue(this,'<?php echo e($row_product_listing_filter->column_name); ?>')" multiple>
												<?php echo e(GetOption(['table' => $row_product_listing_filter->select_table, 'value' => $row_product_listing_filter->select_value_column , 'label' => $row_product_listing_filter->select_label_column,'selected_value' => $selected_value,'column_name'=>$row_product_listing_filter->column_name])); ?>

											</select>
										<?php endif; ?>


											<?php
												$radio_flag = false;
												if($id != NULL && isset($selected_smart_filter[$row_product_listing_filter->column_name])) {
													$value = $selected_smart_filter[$row_product_listing_filter->column_name][$row_product_listing_filter->column_name];
													$radio_flag = true;
												}
											?>

											<!-- main div -->
											<div class="div-main mt-3" id="div_main_<?php echo $row_product_listing_filter->id; ?>" >
												<div >
													<div class="row">
														<div class="col-sm-3">
															<label for="equals_<?php echo $row_product_listing_filter->id; ?>">Equals<label>
														</div>
														<div class="col-sm-9">
															<input type="text" style="width:100%" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][equals]" id="equals_<?php echo $row_product_listing_filter->id; ?>" onkeyup="CopyFieldValue('main',<?php echo $row_product_listing_filter->id; ?>)" value= "<?php if($radio_flag == true && $value == 'equals') echo $selected_smart_filter[$row_product_listing_filter->column_name][$value];?>">
														</div>
													</div>
													<div class="row mt-2">
														<div class="col-sm-3">
															<label onclick="showAdvanceFilterDiv('<?php echo $row_product_listing_filter->id; ?>')">Advanced<label>
														</div>
													</div>
													<div class="solid" style="border-top: 1px solid #bbb;"></div>
												</div>
												<div class="main-filters-buttons mt-1">
													<div class="row">

														<div class="col-sm-12 text-right">
														<button type="button" class="btn btn-warning" onclick="ClearFilter('<?php echo $row_product_listing_filter->id; ?>',this)">Clear</button>
															<button type="button" class="btn btn-danger" onclick="openFilterDropdown('<?php echo $row_product_listing_filter->id; ?>',this)">Cancel</button>
															<button type="button" class="btn btn-success"  onclick="updateFilter('<?php echo $row_product_listing_filter->id; ?>','<?php echo e($row_product_listing_filter->column_name); ?>',this)">Update</button>
														</div>
													</div>
												</div>
											</div>

											<!-- advanced div  -->
											<div class="div-advanced  mt-3" id="div_advanced_<?php echo $row_product_listing_filter->id; ?>" style="display:none">
												<div class="row ">
													<div class="col-sm-1">
														<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_equals" value="equals" checked>
													</div>
													<div class="col-sm-8">
														<div class="row">
															<div class="col-sm-6">
																<label for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_equals">Equals</label>
															</div>
															<div class="col-sm-6">
																<input type="text" class="filter-input-text" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][equals]" id="adv_equals_<?php echo $row_product_listing_filter->id; ?>" onClick="checkRadio('rad_<?php echo e($row_product_listing_filter->column_name); ?>_equals')" onkeyup="CopyFieldValue('adv',<?php echo $row_product_listing_filter->id; ?>)" value= "<?php if($radio_flag == true && $value == 'equals') echo $selected_smart_filter[$row_product_listing_filter->column_name][$value];?>">
															</div>
														</div>
													</div>
												</div>

												<div class="row mt-2">
													<div class="col-sm-3">
														<label onclick="hideAdvanceFilterDiv('<?php echo $row_product_listing_filter->id; ?>')">Advanced<label>
													</div>
												</div>
												<div class="solid" style="border-top: 1px solid #bbb;"></div>
												<div class="advance-filters">
													<div class="row mt-2">

														<div class="col-sm-1">
															<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_include_only" value="include_only" <?php if($radio_flag == true && $value == 'include_only') echo 'checked';?>>
														</div>
														<div class="col-sm-8">
															<div class="row">
																<div class="col-sm-6">
																	<label  for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_include_only">Include only</label>
																</div>
																<div class="col-sm-6">
																	<input type="text" class="filter-input-text"  name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][include_only]" id="" onClick="checkRadio('rad_<?php echo e($row_product_listing_filter->column_name); ?>_include_only')" value= "<?php if($radio_flag == true && $value == 'include_only') echo $selected_smart_filter[$row_product_listing_filter->column_name][$value];?>">
																</div>
															</div>
														</div>
													</div>
													<div class="row mt-2">
														<div class="col-sm-1">
															<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" value="exclude" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_exclude" <?php if($radio_flag == true && $value == 'exclude') echo 'checked';?> >
														</div>
														<div class="col-sm-8">
															<div class="row">
																<div class="col-sm-6">
																	<label for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_exclude">Exclude</label>
																</div>
																<div class="col-sm-6">
																	<input type="text" class="filter-input-text"  name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][exclude]"  onClick="checkRadio('rad_<?php echo e($row_product_listing_filter->column_name); ?>_exclude')" value= "<?php if($radio_flag == true && $value == 'exclude') echo $selected_smart_filter[$row_product_listing_filter->column_name][$value];?>">
																</div>
															</div>
														</div>
													</div>

													<div class="row mt-2">
														<div class="col-sm-1">
															<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" value="does_not_equals" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_does_not_equals" <?php if($radio_flag == true && $value == 'does_not_equals') echo 'checked';?>>
														</div>
														<div class="col-sm-8">
															<div class="row">
																<div class="col-sm-6">
																	<label for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_does_not_equals">Does not equals</label>
																</div>
																<div class="col-sm-6">
																	<input type="text" class="filter-input-text"  name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][does_not_equals]" onClick="checkRadio('rad_<?php echo e($row_product_listing_filter->column_name); ?>_does_not_equals')" value= "<?php if($radio_flag == true && $value == 'does_not_equals') echo $selected_smart_filter[$row_product_listing_filter->column_name][$value];?>">
																</div>
															</div>
														</div>
													</div>
													<div class="row mt-1">
														<div class="col-sm-1">
															<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" value="contains" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_contains" <?php if($radio_flag == true && $value == 'contains') echo 'checked';?>>
														</div>
														<div class="col-sm-8">
															<div class="row">
																<div class="col-sm-6">
																	<label for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_contains">Contains</label>
																</div>
																<div class="col-sm-6">
																	<input type="text" class="filter-input-text" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][contains]" onClick="checkRadio('rad_<?php echo e($row_product_listing_filter->column_name); ?>_contains')" value= "<?php if($radio_flag == true && $value == 'contains') echo $selected_smart_filter[$row_product_listing_filter->column_name][$value];?>">
																</div>
															</div>
														</div>
													</div>
													<div class="row mt-1">
														<div class="col-sm-1">
															<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" value="starts_with" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_starts_with" <?php if($radio_flag == true && $value == 'starts_with') echo 'checked';?>>
														</div>
														<div class="col-sm-8">
															<div class="row">
																<div class="col-sm-6">
																	<label for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_starts_with">Starts with</label>
																</div>
																<div class="col-sm-6">
																	<input type="text" class="filter-input-text" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][starts_with]" onClick="checkRadio('rad_<?php echo e($row_product_listing_filter->column_name); ?>_starts_with')" value= "<?php if($radio_flag == true && $value == 'starts_with') echo $selected_smart_filter[$row_product_listing_filter->column_name][$value];?>">
																</div>
															</div>
														</div>
													</div>
													<div class="row mt-1">
														<div class="col-sm-1">
															<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" value="does_not_starts_with" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_does_not_starts_with" <?php if($radio_flag == true && $value == 'does_not_starts_with') echo 'checked';?>>
														</div>
														<div class="col-sm-8">
															<div class="row">
																<div class="col-sm-6">
																	<label for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_does_not_starts_with">Does not starts with</label>
																</div>
																<div class="col-sm-6">
																	<input type="text" class="filter-input-text" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][does_not_starts_with]" onClick="checkRadio('rad_<?php echo e($row_product_listing_filter->column_name); ?>_does_not_starts_with')" value= "<?php if($radio_flag == true && $value == 'does_not_starts_with') echo $selected_smart_filter[$row_product_listing_filter->column_name][$value];?>">
																</div>
															</div>
														</div>
													</div>
													<div class="row mt-1">
														<div class="col-sm-1">
															<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" value="ends_with" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_ends_with" <?php if($radio_flag == true && $value == 'ends_with') echo 'checked';?>>
														</div>
														<div class="col-sm-8">
															<div class="row">
																<div class="col-sm-6">
																	<label for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_ends_with">Ends with</label>
																</div>
																<div class="col-sm-6">
																	<input type="text"class="filter-input-text" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][ends_with]" onClick="checkRadio('rad_<?php echo e($row_product_listing_filter->column_name); ?>_ends_with')" value= "<?php if($radio_flag == true && $value == 'ends_with') echo $selected_smart_filter[$row_product_listing_filter->column_name][$value];?>">
																</div>
															</div>
														</div>
													</div>
													<div class="row mt-1">
														<div class="col-sm-1">
															<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" value="does_not_ends_with" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_does_not_ends_with" <?php if($radio_flag == true && $value == 'does_not_ends_with') echo 'checked';?>>
														</div>
														<div class="col-sm-8">
															<div class="row">
																<div class="col-sm-6">
																	<label for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_does_not_ends_with">Does not ends with</label>
																</div>
																<div class="col-sm-6">
																	<input type="text"class="filter-input-text" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][does_not_ends_with]" onClick="checkRadio('rad_<?php echo e($row_product_listing_filter->column_name); ?>_does_not_ends_with')" value= "<?php if($radio_flag == true && $value == 'does_not_ends_with') echo $selected_smart_filter[$row_product_listing_filter->column_name][$value];?>">
																</div>
															</div>
														</div>
													</div>
													<?php if($row_product_listing_filter->label_name == 'ETIN'): ?>
													<div class="row mt-1">
														<div class="col-sm-1">
															<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" value="multiple" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_multiple" <?php if($radio_flag == true && $value == 'multiple') echo 'checked';?>>
														</div>
														<div class="col-sm-8">
															<div class="row">
																<div class="col-sm-6">
																	<label for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_multiple">Multiple</label>
																</div>
																<div class="col-sm-6">
																	<input type="text"class="filter-input-text" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][multiple]" onClick="checkRadio('rad_<?php echo e($row_product_listing_filter->column_name); ?>_multiple')" value= "<?php if($radio_flag == true && $value == 'multiple') echo $selected_smart_filter[$row_product_listing_filter->column_name][$value];?>">
																</div>
															</div>
														</div>
													</div>
													<?php endif; ?>
													<div class="row mt-2">
														<div class="col-sm-1">
															<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" value="is_blank" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_is_blank" <?php if($radio_flag == true && $value == 'is_blank') echo 'checked';?>>
														</div>
														<div class="col-sm-6">
															<label for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_is_blank">Is blank</label>
														</div>
													</div>
													<div class="row mt-1">
														<div class="col-sm-1">
															<input type="radio" name="filter_val[<?php echo $row_product_listing_filter->column_name; ?>][<?php echo $row_product_listing_filter->column_name; ?>]" value="is_not_blank" id="rad_<?php echo e($row_product_listing_filter->column_name); ?>_is_not_blank" <?php if($radio_flag == true && $value == 'is_blank') echo 'checked';?>>
														</div>
														<div class="col-sm-6">
															<label for="rad_<?php echo e($row_product_listing_filter->column_name); ?>_is_not_blank">Is not blank</label>
														</div>
													</div>
												</div>
												<div class="solid mt-2" style="border-top: 1px solid #bbb;"></div>
												<div class="advance-filters-buttons mt-2">
													<div class="row">

														<div class="col-sm-12 text-right">
															<button type="button" class="btn btn-warning" onclick="ClearFilter('<?php echo $row_product_listing_filter->id; ?>',this)">Clear</button>
															<button type="button" class="btn btn-danger" onclick="openFilterDropdown('<?php echo $row_product_listing_filter->id; ?>',this)">Cancel</button>
															<button type="button" class="btn btn-success" onclick="updateFilter('<?php echo $row_product_listing_filter->id; ?>','<?php echo e($row_product_listing_filter->column_name); ?>',this)">Update</button>
														</div>
													</div>
												</div>
											</div>
											</div>
										</div>
								</div>

							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php endif; ?>
						<button type="button" id="clear_all_filters" class="btn btn-primary">
							Clear All Filters
						</button>
					</form>

						<table id="datatable" class="table table-bordered text-center" style="width:100%">
							<thead>
								<tr>
									<?php if($product_listing_filter): ?>
										<?php $__currentLoopData = $product_listing_filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row_product_listing_filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<th scope="col"><?php echo e($row_product_listing_filter->label_name); ?></th>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									<?php endif; ?>
									<th>Action</th>
								</tr>
							</thead>

							<tbody>

							</tbody>
						</table>
					</div>
			</div>
		</div>
	</div>
	<!-- end of col-->
</div>

<div class="modal fade" id="modelFilters">
  <div class="modal-dialog" style="width: 25%;">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header" style="background-color:#f7f7f7 !important">
        <h4 class="modal-title">Show / Hide Filters</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
			<form action="" method='POST' id="filter_visibility_form">
				<?php echo csrf_field(); ?>
				<ul class="" id="filter_visibility"  style="list-style-type:none;">
					<?php if($product_listing_filter): ?>
						<?php $__currentLoopData = $product_listing_filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row_product_listing_filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<li class="m-2"><label for="hide_show_<?php echo e($row_product_listing_filter->id); ?>"><input id="hide_show_<?php echo e($row_product_listing_filter->id); ?>" type="checkbox" name='filters[]' value="<?php echo e($row_product_listing_filter->id); ?>" onclick="ShowHideFilters(this,'<?php echo e($row_product_listing_filter->column_name); ?>')" <?php if((!empty($visible_filters) && in_array($row_product_listing_filter->id,$visible_filters)) || $id == NULL) echo 'checked';?>><span class="font-weight-bold ml-2"><?php echo e($row_product_listing_filter->label_name); ?></span></label></li>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php endif; ?>
				</ul>
			</form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="modelColumns">
  <div class="modal-dialog modal-xl" style="width: 25%;">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header" style="background-color:#f7f7f7 !important">
        <h4 class="modal-title">Show / Hide Columns</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
		<div class="row">
			<div class="col-md-1">
				<input type="checkbox" style="zoom:1.3" name="select_unselect_all_columns" id="select_unselect_all_columns">
			</div>
			<div class="col-md-6">
				<label for="select_unselect_all_columns">Select/Unselect All</label>
			</div>
		</div>
		<form action="javascript:void(0);" method='POST' id="column_visibility_form">
		<?php echo csrf_field(); ?>
			<div class="row">
				<ul class="" id="#column_visibility"  style="list-style-type:none;">
					<?php if($product_listing_filter): ?>
						<?php $__currentLoopData = $product_listing_filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row_product_listing_filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<li class="m-2"><label for="hide_show_column_<?php echo e($row_product_listing_filter->id); ?>"><input id="hide_show_column_<?php echo e($row_product_listing_filter->id); ?>" class="listing-filter-columns" type="checkbox" name='columns[]' value="<?php echo e($row_product_listing_filter->sorting_order); ?>" onclick="ShowHideColumn(this,'<?php echo e($row_product_listing_filter->sorting_order); ?>')"  <?php if((!empty($visible_filters) && in_array($row_product_listing_filter->sorting_order,$visible_columns)) || ($id == NULL && $row_product_listing_filter->is_default == 1)) echo 'checked';?>><span class="font-weight-bold ml-2"><?php echo e($row_product_listing_filter->label_name); ?></span></label></li>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php endif; ?>
				</ul>
			</div>
            <div class="modal-footer">
                <?php if(isset($id)): ?>
                <!-- <button type="button" class="btn btn-primary" id="btn_save_smart_filter">
                    Close
                </button> -->
                <?php else: ?>
                <!-- <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> -->
                <?php endif; ?>
				<button type="button" class="btn btn-danger" data-dismiss="modal" onClick="GetActiveProducts()">Close</button>
            </div>
		</form>
      </div>

      <!-- Modal footer -->
      

    </div>
  </div>
</div>

<div class="modal fade" id="modelSmartFilters">
  <div class="modal-dialog modal-lg" style="width: 70%;">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header" style="background-color:#f7f7f7 !important">
        <h4 class="modal-title">Smart Filters</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
			<!-- <ul class="" id="#smart_filters"  style="list-style-type:none;">
				<?php if($smart_filters): ?>
					<?php $__currentLoopData = $smart_filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $smart_fil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<li class="m-2"><a href="<?php echo e(url('/masterparoducts_approved')); ?>/<?php echo e($smart_fil->id); ?>" class="font-weight-bold ml-2" style="<?php if($id != NULL && $smart_fil->id == $id) echo 'color: #19bef4';?>"><?php echo e($smart_fil->filter_name); ?></a></li>
						$smart_fil->productListingFilterList($smart_fil->visible_filters)
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php endif; ?>
			</ul> -->
			<table class="table table-bordered text-center">

				<?php if($smart_filters): ?>
					<tr>
						<td>Fillter name</td>
						<td>Apply Fillter</td>
						<td>Visible Column</td>
					</tr>
					<?php $__currentLoopData = $smart_filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $smart_fil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td><a href="<?php echo e(url('/masterparoducts_approved')); ?>/<?php echo e($smart_fil->id); ?>" class="font-weight-bold ml-2" style="<?php if($id != NULL && $smart_fil->id == $id) echo 'color: #19bef4';?>"><?php echo e($smart_fil->filter_name); ?></a></td>
							<td><?php echo e($smart_fil->productListingFilterList($smart_fil->visible_filters)); ?></td>
							<td><?php echo e($smart_fil->productListingFilterList($smart_fil->visible_columns)); ?></td>
						</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php endif; ?>
			</table>
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="modelBulkActions">
  <div class="modal-dialog" style="width: 25%;">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header" style="background-color:#f7f7f7 !important">
        <h4 class="modal-title">Bulk Actions</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
			<ul class="" id="#filter_visibility"  style="list-style-type:none;">
				<!-- <li class="m-2"><a href="#" class="font-weight-bold ml-2" onclick="ExportDatatable('excel')">Excel</a></li>
				<li class="m-2"><a href="#" class="font-weight-bold ml-2" onclick="ExportDatatable('pdf')">PDF</a></li> -->
				<li class="m-2"><a href="#" class="font-weight-bold ml-2" id="btn_export_pdf" onclick="ExportPDF('pdf')">Pdf</a></li>
				<li class="m-2"><a href="#" class="font-weight-bold ml-2" id="btn_export_excel" onclick="ExportExcel('excel')">Excel</a></li>
			</ul>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="modelSaveASSmartFilter">
  <div class="modal-dialog" style="width: 25%;">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header" style="background-color:#f7f7f7 !important">
        <h4 class="modal-title">Smart Filter</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
			<form action="javascript:void(0);" method="POST" id="save_as_smart_filters_form">
				<input type="hidden" name="type" value="product">
				<div class="form-group">
					<label for="filter_name">Filter Name</label>
					<input type="text" class="form-control" name="filter_name" id="filter_name">
                    <input type="hidden" name="column_orders" id="column_orders" value="">
				</div>
			</form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="btn_save_as_smart_filter">Save</button>
      </div>

    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
	<script src="<?php echo e(asset('assets/js/vendor/echarts.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/es5/echart.options.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/es5/dashboard.v2.script.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/vendor/sweetalert2.min.js')); ?>"></script>
	<script src="<?php echo e(asset('assets/js/sweetalert.script.js')); ?>"></script>
	<script src="https://phpcoder.tech/multiselect/js/jquery.multiselect.js"></script>

<script type="text/javascript">
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	GetActiveProducts(1);

	//Active Product List
	function GetActiveProducts(ini = ''){
		$("#preloader").show();
		if(ini == ''){
			var url = '<?php echo e(route('getOptimizedMasterproductsFilter')); ?>';
		}else{
			var url = '<?php echo e(route('getOptimizedMasterproducts')); ?>';
		}
		table1 = $('#datatable').DataTable({
			// dom:"Bfrtip",
			paging:   true,
            destroy: true,
            responsive: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
			colReorder: true,
			searching:true,
            colReorder: {
                order: [<?php
                    if(!empty($smart_filters) && count($smart_filters) && isset($id)){
                        foreach($smart_filters as $filter){
                            if($filter->id == $id){
                                echo $filter['column_orders'];
                            }

                        }
                    }

                 ?>  ]
            },
			// scrollX: true,
            // stateSave: true,

			lengthMenu: [[10,25, 100, -1], [10, 25, 100, "All"]],
			pageLength: 10,

			ajax:{
                    url: url,
                    method:'POST',
                    data: function(d) {
						// console.log(d);
						var frm_data = $('#form_filters').serializeArray();
						$.each(frm_data, function(key, val) {
							d[val.name] = val.value;
						});
					}
                },
            columns: [
				<?php
					if($product_listing_filter){
						foreach($product_listing_filter as  $key => $row_product_listing_filter){
                            echo "{data: '".$row_product_listing_filter->column_name."', name: '".$row_product_listing_filter->column_name."',defaultContent:'', searchable: false},";
						}
					}
				?>
				{data:'action', name: 'action'}
            ],
				// columnDefs: [
				// 	{
				// 		"targets": [<?php echo e($hidden_cols); ?>],
				// 		"visible": false,
				// 	},
			// ],
            oLanguage: {
                "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:",

            },
			fnInitComplete: function (oSettings, json) {
				$("#preloader").hide();
			}

        });
        var col_order = table1.colReorder.order();
        table1.colReorder.order(col_order)
        // console.log(col_order)
        table1.on( 'column-reorder', function ( e, settings, details ) {
            $("#btn_open_save_as_modal").css("display", "");
            var order = table1.colReorder.order();
            $('#column_orders').val(order)
			$('#btn_save_smart_filter').show();
        } );
		$('.listing-filter-columns').each(function(e){
        	//    console.log($(this).val(), this.checked,e)
		   if(this.checked === false){
				table1.column( $(this).val() ).visible( false );
		   }
		});
    }



	$(document).on('click','#btn_save_as_smart_filter',function(){
		$(this).attr("disabled", true);
		var form_data = $('#save_as_smart_filters_form, #filter_visibility_form, #column_visibility_form, #form_filters').serialize()

		$.ajax({
            type: "POST",
			url: '<?php echo e(route('smart_filters.store')); ?>',
            data: form_data,
            success: function( response ) {
                if(response.error == false){
                    toastr.success(response.msg);
					$("#modelSaveASSmartFilter").modal('hide');
					location.replace(response.url);
                }else{
                    $("#btn_save_as_smart_filter").attr("disabled", false);
                    toastr.error(response.msg);
                }
            },
			error: function(data){
                $("#btn_save_as_smart_filter").attr("disabled", false);
                var errors = data.responseJSON;
                $.each( errors.errors, function( key, value ) {
                    var ele = "#"+key;
                    $(ele).addClass('error');
                    $('<label class="error">'+ value +'</label>').insertAfter(ele);
                });
          	}
        })
        return false;
	});

	$(document).on('click','#btn_save_smart_filter',function(){
		$(this).attr("disabled", true);
		var form_data = $('#filter_visibility_form, #column_visibility_form, #form_filters, #save_as_smart_filters_form').serialize() + "&id=" + <?php echo e($id); ?>


		$.ajax({
            type: "POST",
			url: '<?php echo e(route('smart_filters.update_smart_filter')); ?>',
            data: form_data,
            success: function( response ) {
                if(response.error == false){
                    toastr.success(response.msg);
					// GetActiveProducts();
					// location.reload();
                }else{
                    $("#btn_save_smart_filter").attr("disabled", false);
                    toastr.error(response.msg);
                }
            },
        })
        return false;
	});

	$(document).on('click','#clear_all_filters',function(){
		// $('#form_filters').trigger("reset");
		// $("select").val('').change();
		// GetActiveProducts();
		location.reload();
	});

	function ExportPDF(v){
		// $('#btn_export_pdf').attr("disabled","disabled");
        $('#modelBulkActions').modal('toggle')
        $("#preloader").css("display","block")
		$("#btn_export_pdf").off('click');

		var id = '';
		<?php if(isset($id) && $id != NULL){ ?>
		id = <?php echo $id;?>
		<?php }	?>

		var frm_data = $('#form_filters').serializeArray();
		frm_data.push({ name: "id", value: id });
		frm_data.push({ name: "type", value: 'pdf' });
		$.ajax({
            type: "POST",
			url: '<?php echo e(route('exportMasterProducts')); ?>',
			data: frm_data,
			xhrFields: {
                responseType: 'blob'
            },
            success: function( response ) {
				const d = new Date();
                var blob = new Blob([response]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "Cranium_"+"<?php echo e(date('Ymd')); ?>"+".pdf";
                link.click();
				DeletePdf();
                $("#preloader").css("display","none")
            },
        });
		return false;
	}

	function DeletePdf(){
		$.ajax({
			type: "GET",
			url: '<?php echo e(route('deletepdf')); ?>',
			success: function( response ) {

			},
		});
		return true;
	}

	function ExportExcel(v){
        $('#modelBulkActions').modal('toggle')
        $("#preloader").css("display","block");
		$('#btn_export_excel').attr("disabled","disabled");
		var id = '';
		<?php if(isset($id) && $id != NULL){ ?>
		id = <?php echo $id;?>
		<?php }	?>

		var frm_data = $('#form_filters').serializeArray();
		frm_data.push({ name: "id", value: id });
		frm_data.push({ name: "type", value: 'excel' });
		$.ajax({
            type: "POST",
			url: '<?php echo e(route('exportMasterProducts')); ?>',
			data: frm_data,
			xhrFields: {
                responseType: 'blob'
            },
            success: function( response,status, xhr ) {
				var disposition = xhr.getResponseHeader('content-disposition');
                var blob = new Blob([response],{
					type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
				});

                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "Cranium_"+"<?php echo e(date('Ymd')); ?>"+".xlsx";
				document.body.appendChild(link);
                link.click();
				document.body.removeChild(link);
                $("#preloader").css("display","none")
            },

        });
	}

	//Check-Uncheck All
	$('#select_unselect_all_columns').on('click',function(){
        if(this.checked){
			// $('.dropdown-filter').show();
            $('.listing-filter-columns').each(function(){
                this.checked = true;
            });
			var colCount = table1.columns().header().length;
			for(let i=0;i<colCount;i++){

				table1.column( i ).visible( true );
				$('.fl_'+i).css('display','inline-block');
			}

        }else{
			// $('.dropdown-filter').hide();
             $('.listing-filter-columns').each(function(){
                this.checked = false;
            });
			var colCount = table1.columns().header().length;
			for(let i=0;i<colCount;i++){
				table1.column( i ).visible( false );
				$('.fl_'+i).css('display','none');
			}
        }
		$('#btn_open_save_as_modal').show();
		$('#btn_save_smart_filter').show();
    });

	$('#brand_main_filter').multiselect({
		placeholder: 'Select Brands',
		search: true
	});
	$('#ifd_main_filter').multiselect({
		placeholder: 'Item Form Description',
		search: true
	})
	$('#upc_main_filter').multiselect({
		placeholder: 'UPC(s)',
		search: true
	})
	$('#supp_main_filter').multiselect({
		placeholder: 'Select Supplier(s)',
		search: true
	})
	$('#listing_name_main_filter').multiselect({
		placeholder: 'Listing Name(s)',
		search: true
	})
	$('#etin_main_filter').multiselect({
		placeholder: 'Select ETIN(s)',
		search: true
	})
	$('#allergens_main_filter').multiselect({
		placeholder: 'Select Allergen(s)',
		search: true
	})
	$('#product_tags_main_filter').multiselect({
		placeholder: 'Select Product Tag(s)',
		search: true
	})



	function ShowHideColumn(obj,column){
		if(obj.checked){
			table1.column( column ).visible( true );
			$('.fl_'+column).css('display','inline-block');
        }else{
			table1.column( column ).visible( false );
			$('.fl_'+column).css('display','none');
        }
		$('#btn_open_save_as_modal').show();
		$('#btn_save_smart_filter').show();
	}

	function ExportDatatable(type){
		if(type == 'excel'){
			table1.button( '.buttons-excel' ).trigger();
		}
		if(type == 'pdf'){
			table1.button( '.buttons-pdf' ).trigger();
		}
	}

	function showAdvanceFilterDiv(div_id){
		var main_value = $('#equals_'+div_id).val();
		$('#adv_equals_'+div_id).val(main_value);
		$('#div_main_'+div_id).hide();
		$('#div_advanced_'+div_id).show();
	}

	function hideAdvanceFilterDiv(div_id){
		var adv_value = $('#adv_equals_'+div_id).val();
		$('#equals_'+div_id).val(adv_value);
		$('#div_main_'+div_id).show();
		$('#div_advanced_'+div_id).hide();
	}

	function openFilterDropdown(dropdown_id,$this) {
		//$('#filter_dropdown_'+dropdown_id).toggle("show");
		$($this).parents('.dropdown').find('a.dropdown-toggle').dropdown('toggle');
	}

	function CopyFieldValue(type,div_id){
		if(type == 'adv'){
			var adv_value = $('#adv_equals_'+div_id).val();
			$('#equals_'+div_id).val(adv_value);
		}else{
			var main_value = $('#equals_'+div_id).val();
			$('#adv_equals_'+div_id).val(main_value);
		}
	}

	function checkRadio(key){
		console.log(key);
		$("#"+key).prop('checked',true);
	}

	function updateFilter(dropdown_id,column_name,$this){
		// $('#filter_dropdown_'+dropdown_id).toggle("show");
		$('#btn_open_save_as_modal').show();
		$('#btn_save_smart_filter').show();
		$('#dropdownMenuLink'+dropdown_id).removeClass('btn-secondary');
		$('#dropdownMenuLink'+dropdown_id).addClass('btn-warning');
		var id = $("#main_filter_"+column_name).val($("#drop_down_"+column_name).val())
		$($this).parents('.dropdown').find('a.dropdown-toggle').dropdown('toggle');
		GetActiveProducts();
	}
	
	function refreshdatatable() {
		GetActiveProducts(1);
	}

	function ClearFilter(dropdown_id,$this){
		$('#filter_dropdown_'+dropdown_id).find('input:text, select').each(function () {
			$(this).val('');
		});
		$("#main_filter_ETIN").val('');
		$('#dropdownMenuLink'+dropdown_id).removeClass('btn-warning');
		$('#dropdownMenuLink'+dropdown_id).addClass('btn-secondary');
		$($this).parents('.dropdown').find('a.dropdown-toggle').dropdown('toggle');
		// $('#filter_dropdown_'+dropdown_id).toggle("show");
		GetActiveProducts();
	}

	function GetSelectedValue($this,column_name){
		var id = $("#main_filter_"+column_name).val($("#drop_down_"+column_name).val())
		// GetActiveProducts();
		// var id = $("#main_filter_"+column_name)
		// var id_val = id.val();
		// if(id_val == ''){
		// 	id.val($($this).val());
		// }else{
		// 	var id_val_array = id_val.split('#');
		// 	id_val_array.push($($this).val());
		// 	id.val(id_val_array.join('#'));
		// }
	}

	$(document).on('click', '.table-responsive .dropdown-menu', function (e) {
		e.stopPropagation();
	});



	$('.select2').on("select2:unselecting", function(e){
		$(this).closest('.dropdown-menu').addClass('show1');
		console.log($(this).closest('.dropdown-menu').parent().html());


	});

	$(document).mouseup(function(e)
	{
		var container = $(".dropdown-menu");
		// if the target of the click isn't the container nor a descendant of the container
		if (!container.is(e.target) && container.has(e.target).length === 0)
		{
			$(".dropdown-menu").removeClass('show1');
		}
	});
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/cranium/optimize_product_list.blade.php ENDPATH**/ ?>