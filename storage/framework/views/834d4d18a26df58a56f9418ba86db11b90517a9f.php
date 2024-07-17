
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
<div class="breadcrumb">
    <div class="form-group col-12">
        <h3>Add Cycle Count Schedule</h3>
    </div>
    <div class="form-group col-2">
        <label for="warehouses" class="ul-form__label">Warehouse<span class="text-danger">*</span></label>
        <select onChange="userchange(this)" id="warehouses" name="warehouses" class="form-control select2" >
             <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($warehouse->warehouses); ?>"><?php echo e($warehouse->warehouses); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="form-group col-2">
        <label class="ul-form__label">Schedule Date</label>
        <input type="date" class="form-control" id="schedule_date" name="schedule_date">
    </div>
    <div class="form-group col-2">
        <label class="ul-form__label">Count By<span class="text-danger">*</span></label>
        <select  id="countBy" name="countBy" class="form-control select2" >
            <option value="product">Product</option>
            <option value="location">Location</option>
        </select>
    </div>
    <div class="col-2 js-client">
        <label for="rules" class="ul-form__label">Select Client</label>
        <select name="client_id" id="client_id"  class="form-control select2">
            <option value=''>Select Client</option>
            <?php $__currentLoopData = $client; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key_c => $row_c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option  value="<?php echo e($key_c); ?>"><?php echo e($row_c); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-2">
        <label for="rules" class="ul-form__label">Select User</label>
        <select name="user_id" id="user_id"  class="form-control select2">
            <option value=''>Select User</option>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key_c => $row_c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key_c); ?>"><?php echo e($row_c); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-12">
        <button onclick="GetActiveProducts()" class="btn  btn-primary m-1">Create</button>
        <a href="<?php echo e(route('warehousemanagment.cyclecount.index')); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
        <!-- <a href="" class="btn btn-outline-secondary m-1">Cancel</a> -->
    </div>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row js-product d-none">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card o-hidden mb-4 js-product d-none">
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
                        <a onclick="GetActiveProducts()"><img src="<?php echo e(asset('assets/images/refresh.png')); ?>" style="width: 25px; float: right; cursor: pointer;"></a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                <form action="javascript:void(0);" id="form_filters" method="POST">
						<?php echo csrf_field(); ?>
                        <input type="hidden" id="warehouse" name="warehouse">
						<?php if($product_listing_filter): ?>
							<?php $__currentLoopData = $product_listing_filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row_product_listing_filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php $display = false;?>
								<?php if($id != NULL && !in_array($row_product_listing_filter->sorting_order,$hidden_cols_arr)): ?>
									<?php $display = true;?>
								<?php endif; ?>
								<?php if($id == NULL && $row_product_listing_filter->is_default == 1): ?>
									<?php $display = true;?>
								<?php endif; ?>
								<div class="dropdown mb-2 fl_<?php echo e(($row_product_listing_filter->sorting_order+2)); ?>" style="<?php if($display == true) echo "display:inline-block !important"; else echo "display:none"?>">
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
                    <table id="datatable" class="table table-bordered text-center dataTable_filter">
                        <thead>
                            <tr>
                                <!--<th scope="no">Category</th>-->
                                <th >#</th>
                                <th><input type="checkbox" id="all_new_approve" name="all_new_approve"></th>
                                <?php if($product_listing_filter): ?>
                                    <?php $__currentLoopData = $product_listing_filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row_product_listing_filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th scope="col"><?php echo e($row_product_listing_filter->label_name); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button id="newApprove" class="btn btn-primary ml-3">Approve Selected</button>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body js-location d-none">
                <table id="datatable1" class="table table-bordered text-center dataTable_filter">
                    <thead>
                        <tr>
                            <th scope="col" id="idclass">#</th>
                            <th><input type="checkbox" id="all_new_approve1" name="all_new_approve1"></th>
                            <th scope="col">ETIN</th>
                            <th scope="col">address</th>
                            <th scope="col">cur_qty</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button id="newApprove1" class="btn btn-primary ml-3">Approve Selected</button>
                <button id="showMap" class="btn btn-primary ml-3">Show Location</button>
            </div>
            <div class="card-body js-location-image d-none" id="imgdata">
                <div class="table-responsive">
                    <table id="datatablelocation" class="table table-bordered text-center dataTable_filter">
                        <thead>
                            <tr>
                                <!--<th scope="no">Category</th>-->
                                <th >#</th>
                                <th><input type="checkbox" id="all_new_approve2" name="all_new_approve2"></th>
                                <th>Location</th>
                                <th>Temperature</th>
                                <th>Warehouse</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button onClick="locationApprove()" class="btn btn-primary ml-3">Get Location Product</button>
                </div>
            </div>
        </div>
        
    </div>
    <!-- end of col-->
</div>






<!-- </div> -->

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
							<li class="m-2"><label for="hide_show_column_<?php echo e($row_product_listing_filter->id); ?>"><input id="hide_show_column_<?php echo e($row_product_listing_filter->id); ?>" class="listing-filter-columns" type="checkbox" name='columns[]' value="<?php echo e(($row_product_listing_filter->sorting_order+2)); ?>" onclick="ShowHideColumn(this,'<?php echo e(($row_product_listing_filter->sorting_order+2)); ?>')"  <?php if((!empty($visible_filters) && in_array($row_product_listing_filter->sorting_order,$visible_columns)) || ($id == NULL && $row_product_listing_filter->is_default == 1)) echo 'checked';?>><span class="font-weight-bold ml-2"><?php echo e($row_product_listing_filter->label_name); ?></span></label></li>
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
    <!-- end of col -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
    <script src="<?php echo e(asset('assets/js/vendor/sweetalert2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/sweetalert.script.js')); ?>"></script>
    <script src="https://phpcoder.tech/multiselect/js/jquery.multiselect.js"></script>
<script>
    $( function() {
        $( "#schedule_date" ).datepicker({ minDate: 0 ,dateFormat: 'yy-mm-dd'});
        $('#schedule_date').datepicker('setDate', 'today');
    });
    function GetActiveProducts(){
        $('#warehouse').val($("#warehouses").val());
        if($("#countBy").val() == 'location'){
            showMap();
        }
        else{
            $('.js-product').removeClass('d-none');
            $('.js-location').addClass('d-none');
            $('.js-location-image').addClass('d-none');
            table1 = $('#datatable').DataTable({
            paging:   true,
            destroy: true,
            responsive: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            lengthMenu: [[25,50, 100, 500], [25,50, 100, 500]],
            pageLength: 25,
            searching:true,
            ajax:{
                    url: '<?php echo e(route('getactivemasterproducts')); ?>',
                    method:'POST',
                    data: function(d) {
                        // console.log(d);
                        var frm_data = $('#form_filters').serializeArray();
                        $.each(frm_data, function(key, val) {
                            d[val.name] = val.value;
                        });
                         d.client_id_for_cycle_count = $('#client_id').val();
                    }
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'approve_check', name: 'approve_check'},
                <?php
                   // dd($product_listing_filter);
					if($product_listing_filter){
						foreach($product_listing_filter as  $key => $row_product_listing_filter){
                            echo "{data: '".$row_product_listing_filter->column_name."', name: '".$row_product_listing_filter->column_name."',defaultContent:'', searchable: false},";
						}
					}
				?>
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                },
                {
                    orderable: false,
                    targets: 1
                }
            ],
        });
        $('.listing-filter-columns').each(function(e){
        	//    console.log($(this).val(), this.checked,e)
		   if(this.checked === false){
				table1.column( $(this).val() ).visible( false );
		   }
		});
        // $('.listing-filter-columns').each(function(e){
        // 	//    console.log($(this).val(), this.checked,e)
		//    if(this.checked === false){
		// 		table1.column( $(this).val() ).visible( false );
		//    }
		// });
        }
    }
    function showMap() {
        $(".js-location-image").removeClass("d-none");
            $('.js-location').addClass('d-none');
            $('.js-product').addClass('d-none');

            table1 = $('#datatablelocation').DataTable({
            paging:   true,
            destroy: true,
            responsive: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            lengthMenu: [[25,50, 100, 500], [25,50, 100, 500]],
            pageLength: 25,
            ajax:{
                    url: '<?php echo e(route('getlocaionwarehousewise')); ?>',
                    method:'POST',
                    data: function(d) {
                        d['warehouse'] = $("#warehouses").val();
                    }
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'approve_check', name: 'approve_check'},
                {data: 'aisle_name', name: 'location_name'},
                {data: 'storage_type.group_name', name: 'product_temp_id'},
                {data: 'warehouse_name.warehouses', name: 'warehouse_id'},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                },
                {
                    orderable: false,
                    targets: 1
                }
            ],
        });
       



            //$.ajax({
            //    type: "get",
            //    url: '<?php echo e(route('getwarehousesvg')); ?>',
            //    data: {
            //        warehouse : $("#warehouses").val(),
            //    },
            //    success: function( response ) {
            //        $("#imgdata").html(response)
            //    }
            //});
    }
    function locationApprove(){
         var val = [];
        $('.newApproveCheckBox2:checked').each(function(i){
          val[i] = $(this).val();
        });
        if(val.length !== 0){
            
            $('.js-location').removeClass('d-none');
            $('.js-product').addClass('d-none');
            $('.js-location-image').addClass('d-none');
            table1 = $('#datatable1').DataTable({
                // dom:"Bfrtip",
                paging:   true,
                destroy: true,
                responsive: false,
                processing: true,
                serverSide: true,
                autoWidth: false,
                colReorder: true,
                searching:true,
                ajax:{
                        url: '<?php echo e(route('locationcyclenew')); ?>',
                        method:'POST',
                        data: {
                                aisleName : val,
                                warehouse : $("#warehouses").val(),
                                schedule_date : $("#schedule_date").val(),
                                countBy : $("#countBy").val(),
                                //client_id_for_cycle_count :  $('#client_id').val(),
                            },
                    },
                lengthMenu: [[25,50, 100, 500], [25,50, 100, 500]],
                pageLength: 25,
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'approve_check', name: 'approve_check'},
                    {data: 'ETIN', name: 'ETIN'},
                    {data: 'address', name: 'address'},
                    {data: 'cur_qty', name: 'cur_qty'},
                ],
                columnDefs: [
                    {
                        "targets": [ 0 ],
                        "visible": false
                    },
                    {
                        orderable: false,
                        targets: 1
                    },
                ],
                oLanguage: {
                    "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:",

                },
                fnInitComplete: function (oSettings, json) {
                    $("#preloader").hide();
                }

            });
        }
        else{
            alert("Please Select Location");
        }
    };
    $('#all_new_approve').on('click',function(){
        if(this.checked){
            $('.newApproveCheckBox').each(function(){
                this.checked = true;
            });
        }else{
             $('.newApproveCheckBox').each(function(){
                this.checked = false;
            });
        }
    });
    $('#all_new_approve1').on('click',function(){
        if(this.checked){
            $('.newApproveCheckBox1').each(function(){
                this.checked = true;
            });
        }else{
             $('.newApproveCheckBox1').each(function(){
                this.checked = false;
            });
        }
    });
      $('#all_new_approve2').on('click',function(){
        if(this.checked){
            $('.newApproveCheckBox2').each(function(){
                this.checked = true;
            });
        }else{
             $('.newApproveCheckBox2').each(function(){
                this.checked = false;
            });
        }
    });
    $('#newApprove').click(function(){
        var val = [];
        $('.newApproveCheckBox:checked').each(function(i){
          val[i] = $(this).val();
        });
        if(val.length !== 0){
            if(confirm("Are You Sure approve product for cycle count?")){
                $.ajax({
                    type: "POST",
                    url: '<?php echo e(route('approvecyclecount')); ?>',
                    data: {
                        checked : val,
                        warehouse : $("#warehouses").val(),
                        schedule_date : $("#schedule_date").val(),
                        countBy : $("#countBy").val(),
                        client_id_for_cycle_count :  $('#client_id').val(),
                        user_id:$('#user_id').val(),
                    },
                    success: function( response ) {
                        if(response.error == false){
                            toastr.success(response.msg);
                            setTimeout(function(){
                                location.reload();
                            },2000);
                        }else{
                            toastr.error(response.msg);
                        }
                    }
                });
            }
        }else{
            alert("Please Select Product To Approve");
        }
    });
    function updateFilter(dropdown_id,column_name,$this){
        // $('#filter_dropdown_'+dropdown_id).toggle("show");
        $('#btn_open_save_as_modal').show();
        $('#btn_save_smart_filter').show();
        $('#dropdownMenuLink'+dropdown_id).removeClass('btn-secondary');
        $('#dropdownMenuLink'+dropdown_id).addClass('btn-warning');
        var id = $("#main_filter_"+column_name).val($("#drop_down_"+column_name).val())
        $($this).parents('.dropdown').find('a.dropdown-toggle').dropdown('toggle');
        GetfillterProduct();
    }    

    $(document).on('click', '.table-responsive .dropdown-menu', function (e) {
		e.stopPropagation();
	});
    
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

    function GetfillterProduct(){
        $("#preloader").show();
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
            ajax:{
                    url: '<?php echo e(route('productsfilterwarehouse')); ?>',
                    method:'POST',
                    data: function(d) {
                        // console.log(d);
                        var frm_data = $('#form_filters').serializeArray();
                        $.each(frm_data, function(key, val) {
                            d[val.name] = val.value;
                        });
                        d.client_id_for_cycle_count = $('#client_id').val();
                    }
                },
            lengthMenu: [[25,50, 100, 500], [25,50, 100, 500]],
            pageLength: 25,
            columns: [
                {data: 'id', name: 'id'},
                {data: 'approve_check', name: 'approve_check'},
                <?php
					if($product_listing_filter){
						foreach($product_listing_filter as  $key => $row_product_listing_filter){
                            echo "{data: '".$row_product_listing_filter->column_name."', name: '".$row_product_listing_filter->column_name."',defaultContent:'', searchable: false},";
						}
					}
				?>
            ],
            columnDefs: [
                 {
                    "targets": [ 0 ],
                    "visible": false
                },
                {
                    orderable: false,
                    targets: 1
                },
            ],
            oLanguage: {
                "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:",

            },
            fnInitComplete: function (oSettings, json) {
                $("#preloader").hide();
            }

        });
        $('.listing-filter-columns').each(function(e){
        	//    console.log($(this).val(), this.checked,e)
		   if(this.checked === false){
				table1.column( $(this).val() ).visible( false );
		   }
		});
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
    function ClearFilter(dropdown_id,$this){
        $('#filter_dropdown_'+dropdown_id).find('input:text, select').each(function () {
            $(this).val('');
        });
        $("#main_filter_ETIN").val('');
        $('#dropdownMenuLink'+dropdown_id).removeClass('btn-warning');
        $('#dropdownMenuLink'+dropdown_id).addClass('btn-secondary');
        $($this).parents('.dropdown').find('a.dropdown-toggle').dropdown('toggle');
        // $('#filter_dropdown_'+dropdown_id).toggle("show");
        GetfillterProduct();
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
    function pathClickHandler($this){
        const aisleName = $this.id;
         $('.js-location').removeClass('d-none');
         $('.js-product').addClass('d-none');
         $('.js-location-image').addClass('d-none');
         table1 = $('#datatable1').DataTable({
            // dom:"Bfrtip",
            paging:   true,
            destroy: true,
            responsive: false,
            processing: true,
            serverSide: true,
            autoWidth: false,
            colReorder: true,
            searching:true,
            ajax:{
                    url: '<?php echo e(route('locationcycle')); ?>',
                    method:'POST',
                    data: {
                            aisleName : aisleName,
                            warehouse : $("#warehouses").val(),
                            schedule_date : $("#schedule_date").val(),
                            countBy : $("#countBy").val(),
                        },
                },
            lengthMenu: [[25,50, 100, 500], [25,50, 100, 500]],
            pageLength: 25,
            columns: [
                {data: 'id', name: 'id'},
                {data: 'approve_check', name: 'approve_check'},
                {data: 'ETIN', name: 'ETIN'},
                {data: 'address', name: 'address'},
                {data: 'cur_qty', name: 'cur_qty'},
            ],
            columnDefs: [
                 {
                    "targets": [ 0 ],
                    "visible": false
                },
                {
                    orderable: false,
                    targets: 1
                },
            ],
            oLanguage: {
                "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:",

            },
            fnInitComplete: function (oSettings, json) {
                $("#preloader").hide();
            }

        });
    };
    function GetSelectedValue($this,column_name){ name
        var id = $("#main_filter_"+column_name).val($("#drop_down_"+column_name).val())
    }
    function userchange(element) {
        $.ajax({
            type: "POST",
            url: '<?php echo e(route('changeuser')); ?>',
            data: {
                w_id: $(element).val(),
            },
            success: function(response) {
                if (response.error == false) {
                    var html = '<option value="">Select User</option>';
                    $.each(response.users, function(key, value) {
                        html += '<option value="' + key + '">' + value + '</option>';
                    });
                    $("#user_id").html(html);
                } else {
                    // Handle the error case here
                }
            },
        });
    }

    $('#newApprove1').click(function(){
        var val = [];
        $('.newApproveCheckBox1:checked').each(function(i){
          val[i] = $(this).val();
        });
        if(val.length !== 0){
            if(confirm("Are You Sure approve product for cycle count?")){
                $.ajax({
                    type: "POST",
                    url: '<?php echo e(route('approvecyclecount')); ?>',
                    data: {
                        checked : val,
                        warehouse : $("#warehouses").val(),
                        schedule_date : $("#schedule_date").val(),
                        countBy : $("#countBy").val(),
                    },
                    success: function( response ) {
                        if(response.error == false){
                            toastr.success(response.msg);
                            setTimeout(function(){
                                window.location.href = "/cyclecount"
                            },2000);
                        }else{
                            toastr.error(response.msg);
                        }
                    }
                });
            }
        }else{
            alert("Please Select Product To Approve");
        }
    });
    $('#showMap').click(function(){ showMap(); });
    function checkRadio(key){
		console.log(key);
		$("#"+key).prop('checked',true);
	}
     $('#countBy').change(function(){ 
        if($("#countBy").val() == 'location'){
            $(".js-client").addClass('d-none');
        }
        else{
            $(".js-client").removeClass('d-none');
        }
     });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/WarehouseManagment/cyclecountcreate.blade.php ENDPATH**/ ?>