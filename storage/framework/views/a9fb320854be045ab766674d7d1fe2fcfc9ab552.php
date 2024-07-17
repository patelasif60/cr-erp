
<?php $__env->startSection('page-css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
    <div class="breadcrumb">
        <h1>Suppliers</h1>
        <ul>
            <li><a href="">Packaging & Material</a></li>
            <li>Edit</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Edit New Kits for Packageing</h6>
                </div>
                <form method="POST"  id="packaging_material_form">
                	<?php echo csrf_field(); ?>
                	<input type="hidden" name="supplier_id" value="<?php echo e($packagingMatirials->supplier_id); ?>">
                    <input type="hidden" name="item_form_description" value="<?php echo e($type); ?>">
                    <input type="hidden" name="pagetype" value="<?php echo e($pagetype); ?>">
                    <input type="hidden" name="package_id" id="package_id" value="<?php echo e($id); ?>">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="name" class="ul-form__label">ETIN:<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="ETIN" name="ETIN" placeholder="ETIN" value='<?php echo e($packagingMatirials->ETIN); ?>' readonly>
                                    </div>
                                    <div class="form-group col-md-12">
	                                    <label for="description" class="ul-form__label">Product Description:<span class="text-danger">*</span></label>
	                                    <textarea required name="product_description" id="product_description" cols="10" rows="3" class="form-control" placeholder="Enter Product Description"><?php echo e($packagingMatirials->product_description); ?></textarea>
	                                </div>
                                    <div class="form-group col-md-12">
	                                    <label for="sales_manager" class="ul-form__label">Material Type</label>
	                                    <select class="form-control select2" id="material_type_id" name="material_type_id">
	                                        <option value="">--Select--</option>
	                                        <?php if($materialTypes): ?>
												<?php $__currentLoopData = $materialTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $materialType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($materialType->id); ?>" 
														<?php echo e($packagingMatirials->material_type_id == $materialType->id ? 'selected' : ''); ?>

													>
													<?php echo e($materialType->material_type); ?>

												 </option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
	                                    </select>
                                	</div>
                                    <div class="form-group col-md-12">
                                        <label for="main_point_of_contact" class="ul-form__label">Quantity Per Bundle<span class="text-danger">*</span></label>
                                        <input value='<?php echo e($packagingMatirials->quantity_per_bundle); ?>' required type="text" class="form-control" id="quantity_per_bundle" name="quantity_per_bundle" placeholder="Enter Quantity Per Bundle">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address" class="ul-form__label">Bundle Qty Per Truck Load<span class="text-danger">*</span></label>
                                        <input type="text" required value='<?php echo e($packagingMatirials->bundle_qty_per_truck_load); ?>' class="form-control" id="bundle_qty_per_truck_load" name="bundle_qty_per_truck_load" placeholder="Enter Bundle Qty Per Truck Load">
                                    </div>
                                    <div class="form-group col-md-12">
	                                    <label for="sales_manager" class="ul-form__label">Product Temperature</label>
	                                    <select class="form-control select2" id="product_temperature" name="product_temperature">
	                                       <!--  <option value="">--Select--</option>
	                                        <?php if($producttemp): ?>
												<?php $__currentLoopData = $producttemp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>"
														<?php echo e($packagingMatirials->product_temperature == $value ? 'selected' : ''); ?>

													><?php echo e($value); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?> -->
                                            <option value="Packaging">Packaging</option>
	                                    </select>
                                	</div>
                                    <div class="form-group col-md-12">
                                        <label for="sales_manager" class="ul-form__label">Bluck Price:</label>
                                        <input type="number" class="form-control" value='<?php echo e($packagingMatirials->bluck_price); ?>' min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" name="bluck_price" id="bluck_price" placeholder="Enetr Bulk Price">
                                    </div>
                                    <div class="form-group col-md-12 mt-3">
                                        <label for="has_barcode" class="ul-form__label">Has Scannable Barcode:</label>
                                        <input <?php if (isset($packagingMatirials->has_barcode) && $packagingMatirials->has_barcode == 1) echo 'checked' ?> type="checkbox" id="has_barcode" name="has_barcode" onchange="disableScannableBarcodeTextBox(this)">
                                    </div> 
                                    <div class="form-group col-md-12">
                                        <label for="email" class="ul-form__label">Scannable Barcode:</label>
                                        <input class="form-control" <?php if (!isset($packagingMatirials->has_barcode) || $packagingMatirials->has_barcode != 1) echo 'disabled' ?> value='<?php echo e($packagingMatirials->scannable_barcode); ?>' id="scannable_barcode" name="scannable_barcode" placeholder="Enter Scannable Barcode">
                                    </div>                                  
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group col-md-12">
                                    <label for="weight" class="ul-form__label">Weight (lbs):</label>
                                    <input type="number" class="form-control" id="weight" value='<?php echo e($packagingMatirials->weight); ?>' min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="weight" placeholder="Enter Weight">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="external_length" class="ul-form__label">External Length:</label>
                                    <input type="number" class="form-control" id="external_length" value='<?php echo e($packagingMatirials->external_length); ?>' min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="external_length" placeholder="Enter External Length">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="external_width" class="ul-form__label">External Width:</label>
                                    <input type="number" class="form-control" id="external_width"  value='<?php echo e($packagingMatirials->external_width); ?>' min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="external_width" placeholder="Enter External Width">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="external_height" class="ul-form__label">External Height:</label>
                                    <input type="number" class="form-control" id="external_height" value='<?php echo e($packagingMatirials->external_height); ?>' min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="external_height" placeholder="Enter External Height">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="internal_length" class="ul-form__label">Internal Length:</label>
                                    <input type="number" class="form-control" id="internal_length" value='<?php echo e($packagingMatirials->internal_length); ?>' min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="internal_length" placeholder="Enter Internal Length">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="internal_width" class="ul-form__label">Internal Width:</label>
                                    <input type="number" class="form-control" id="internal_width" value='<?php echo e($packagingMatirials->internal_width); ?>' min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="internal_width" placeholder="Enter Internal Width">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="internal_height" class="ul-form__label">Internal Height:</label>
                                    <input type="number" class="form-control" id="internal_height" value='<?php echo e($packagingMatirials->internal_height); ?>' min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="internal_height" placeholder="Enter Internal Height">
                                </div>                               
                            </div>
                            <div class="col-md-3">
                                <div class="form-group col-md-12">
                                    <label for="status" class="ul-form__label">Status:</label>
                                    <select class="form-control" id="status" name="status" >
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                        <option value="Secondary">Secondary</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="cost" class="ul-form__label">Cost:</label>
                                    <input type="number" class="form-control" value='<?php echo e($packagingMatirials->cost); ?>' min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" id="cost" name="cost" placeholder="Enter Cost">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="new_cost" class="ul-form__label">New Cost:</label>
                                    <input type="number" class="form-control"  value='<?php echo e($packagingMatirials->new_cost); ?>' min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" id="new_cost" name="new_cost" placeholder="Enter New Cost">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="new_cost_date" class="ul-form__label">New Cost Date:</label>
                                     <input type="text" class="form-control flatpickr" value='<?php echo e($packagingMatirials->new_cost_date); ?>' id="new_cost_date" name="new_cost_date">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="acquisition_cost" class="ul-form__label">Acquisition Cost:</label>
                                    <input type="number" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" value='<?php echo e($packagingMatirials->acquisition_cost); ?>' name="acquisition_cost" id="acquisition_cost" class="form-control" placeholder="Enter Capacity Cubic">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="capacity_cubic" class="ul-form__label">Capacity Cubic:</label>
                                    <input type="number" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" value='<?php echo e($packagingMatirials->capacity_cubic); ?>' name="capacity_cubic" id="capacity_cubic" class="form-control" placeholder="Enter Capacity Cubic">
                                </div>
                                <div class="form-group col-md-12">
	                                <label for="phone" class="ul-form__label">Supplier Product Number</label>
	                                <input type="text" class="form-control" value='<?php echo e($packagingMatirials->supplier_product_number); ?>' id="supplier_product_number" name="supplier_product_number" placeholder="Enter Supplier Product Number">
	                            </div>                                <!-- 
                                <div class="form-group col-md-12">
                                    <label for="sales_manager" class="ul-form__label">Product Assigned:</label>
                                    <select class="form-control select2" id="sales_manager" name="sales_manager">
                                        <option value="">--Select--</option>
                                    </select>
                                </div> -->                                
                            </div>
                            <div class="col-md-3">
	                            <div class="form-group col-md-12">
	                                <label for="warehouses_assigned" class="ul-form__label" data-toggle="tooltip" data-placement="top" title="Warehouse(s) stocking this product">Warehouse(s) Assigned <span class="text-danger">*</span></label>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th></th>
                                            <th>Stocked</th>
                                        </tr>
                                        <?php if($warehouse): ?>
											<?php $__currentLoopData = $warehouse; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr>
													<td><?php echo e($warehouses); ?></td>
													<td>
														<input type="checkbox" name="warehouse[]" value="<?php echo e($warehouses); ?>" 
															<?php echo e(in_array($warehouses ,explode(',',$packagingMatirials->warehouses_assigned)) ? 'checked' :''); ?>

														>
													</td>
													<td></td>
												</tr>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
                                    </table>
	                            </div>
	                            <div class="form-group col-md-12">
	                                <label for="sales_manager" class="ul-form__label">Clients Assigned:</label>
	                                <select class="form-control select2" id="clients_assigned" name="clients_assigned">
	                                    <option value="">--Select--</option>
	                                    <?php if($client): ?>
											<?php $__currentLoopData = $client; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($key); ?>"
													<?php echo e($packagingMatirials->clients_assigned == $key ? 'selected' : ''); ?>

												><?php echo e($value); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
	                                </select>
	                            </div>	
                                <div class="form-group col-md-12">
                                    <label for="sales_manager" class="ul-form__label">Channels</label>
                                    <select id="channel_name" class="form-control select2" name="channel_ids[]" multiple>
                                        <option value="">-Select-</option>
                                        <?php if(isset($channels)): ?>
                                            <?php $channel_list = explode(',', $packagingMatirials->channel_ids); ?>                              
                                            <?php $__currentLoopData = $channels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($channel['id']); ?>" <?php if(in_array($channel['id'], $channel_list)) echo 'selected';?>>
                                                    <?php echo e($channel['channel']); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="unit_description" class="ul-form__label">Unit Description</label>
                                    <select onchange="populateProduct()" id="unit_description" class="form-control select2" name="unit_desc">
                                        <option value="">--Select--</option>
                                        <?php if(isset($all_unit_descs)): ?>
                                            <?php $__currentLoopData = $all_unit_descs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($ud); ?>" <?php if ($ud == $packagingMatirials->unit_desc) echo 'selected'; ?>><?php echo e($ud); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                                
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="item_form_description" class="ul-form__label">Item Form Description</label>
                                    <select onchange="populateProduct()" id="item_form_description" class="form-control select2" name="item_form_desc">
                                        <option value="">--Select--</option>
                                        <?php if(isset($all_item_form_descs)): ?>
                                            <?php $__currentLoopData = $all_item_form_descs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($ud); ?>" <?php if ($ud == $packagingMatirials->item_form_desc) echo 'selected'; ?>><?php echo e($ud); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                                
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="sales_manager" class="ul-form__label">Products</label>
                                    <select id="product_name" class="form-control select2" name="product_ids[]" multiple>
                                        <option value="">-Select-</option>
                                        <?php if(isset($products)): ?>
                                            <?php $product_list = explode(',', $packagingMatirials->product_ids); ?>                              
                                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($product['id']); ?>" <?php if(in_array($product['id'], $product_list)) echo 'selected';?>>
                                                    <?php echo e($product['product_name']); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>                            
                        	</div>
                        </div>
                        <hr/>
                        <div class="row">
                            <?php if($warehouse): ?>
                                <?php $__currentLoopData = $warehouse; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-3">
                                        <div class="form-group col-md-12 text-center">
                                            <h5><?php echo e($warehouses); ?></h5>                                    
                                        </div>
                                        <?php
                                            $wh_lower = strtolower($warehouses);
                                        ?>
                                        <?php $__currentLoopData = range(1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $td): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="form-group col-md-12">
                                                <label for="<?php echo e($wh_lower); ?>_td_<?php echo e($td); ?>" class="ul-form__label">Transit Day <?php echo e($td); ?></label>
                                                <input value='<?php echo e(isset($wh_td_count[$warehouses][$td]) ? $wh_td_count[$warehouses][$td] : ''); ?>' type="number" class="form-control" id="<?php echo e($wh_lower); ?>_td_<?php echo e($td); ?>" name="<?php echo e($wh_lower); ?>_td_<?php echo e($td); ?>" placeholder="Max Item for Transit Day <?php echo e($td); ?>">
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                                                      
                                    </div>  
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                        <hr/>
                         <div class="row">
                            <div class="card-header bg-transparent">
                                <h6 class="card-title task-title">Kit Configuration & Packaging Details</h6>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-7">
                                <input type="hidden" id="search_dt">
                                <p><b>Select Components</b></p>
                                <table class="table table-border table-stripped dataTable_filter" id="parent_kit_packaging">
                                    <thead>
                                        <tr>
                                            <td>ETIN</td>
                                            <td>Product Description</td>
                                            <td>Action</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-lg-5">
                                <p><b>Selected Components</b></p>
                                <input type="hidden" id="selected_packages" value="<?php echo e(json_encode($packageKitComponents->pluck('qty','pid'))); ?>">
                                <input type="hidden" id="unit_in_pack" value="0" name="unit_in_pack">
                                <table class="table table-border table-stripped" id="parent_kit_packaging_selected">
                                    <thead>
                                        <tr>
                                            <td>ETIN</td>
                                            <td>Product Description</td>
                                            <td>Qty</td>
                                            <td>Action</td>
                                        </tr>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $packageKitComponents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $packageKitComponentsKey => $packageKitComponentsVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr id="<?php echo e($packageKitComponentsVal->pid); ?>">
                                                <td><?php echo e($packageKitComponentsVal->components_ETIN); ?></td>
                                                <td><?php echo e($packageKitComponentsVal->product_description); ?></td>
                                                <td>
                                                    <input type="hidden" name="kit_components[<?php echo e($packageKitComponentsVal->pid); ?>][components_ETIN]" value="<?php echo e($packageKitComponentsVal->components_ETIN); ?>" id="kit_components_etin">
                                                    <input type="number" class="form-control" name="kit_components[<?php echo e($packageKitComponentsVal->pid); ?>][qty]" value="<?php echo e($packageKitComponentsVal->qty); ?>" id="kit_components_qty" style="width:55px;padding:0px">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="btn btn-danger" onClick="removeProduct(<?php echo e($packageKitComponentsVal->pid); ?>)">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="mc-footer">
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                     <?php if($pagetype == 'packaginglist'): ?>
                                        <a href="<?php echo e(route('listpackagingmatirial.index')); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('suppliers.index')); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
                                    <?php endif; ?>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="selected_packages_qty" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header-->
                <div class="modal-header" style="background-color:#fff;">
                    <h3>Packaging Quantity</h3>
                    <!--Close/Cross Button-->
                    <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
                </div>


                    <div class="modal-body">
                        <label>Qty</label>
                        <input type="number" name="qty" class="form-control" id="qty" style="width:100%;" required/>
                        <input type="hidden" id="pack_id">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="add_pck_qty">Add</button>
                        <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a>
                    </div>

            </div>
        </div>
    </div>
    <!-- end of col -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/validation/jquery.validate.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/validation/additional-methods.min.js')); ?>"></script>
<script type="text/javascript">
	$("#packaging_material_form").validate({
	submitHandler(form){

        matType = $('#material_type_id').val();
        barcode = $('#scannable_barcode').val();

        if (matType == 1 && barcode == '') {
            toastr.error('Barcode is mandatory for Outer Box.');
            return false;
        }
        var checked = $('input[type="checkbox"][name="warehouse[]"]:checked').length;
         if (checked === 0) {
            toastr.error('Please select at least one warehouse');
            return false;
        }

		$(".submit").attr("disabled", true);
        $('div#preloader').show();
		var form_cust = $('#packaging_material_form')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '<?php echo e(route('updatepackagematerial',$packagingMatirials->id)); ?>',
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
 $(document).ready(function () {
    getSupplierPackaging();
    if ($('#product_name').val().length == 0) {
        populateProduct();
    }
 })
 openQtyModal = (id) =>{
    $("#selected_packages_qty").modal('show');
    $("#pack_id").val(id);
}
 getSupplierPackaging = () =>  {
        table = $('#parent_kit_packaging').DataTable({
        destroy: true,
        responsive: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax:{
                url: '<?php echo e(route('getpackagingbysupplier',$packagingMatirials->supplier_id)); ?>',
                method:'GET',
                data: function(d){
                    d.ids = $("#selected_packages").val();
                    d.type = 'kit';
                    d.packageId = $("#package_id").val();
                }            
            },
        rowId: 'id',
        columns: [
            {data: 'ETIN', name: 'ETIN',defaultContent:'-'},
            {data: 'product_description', name: 'product_description',defaultContent:'-'},
            {data: 'action', name: 'action',searchable:false},
        ],
        oLanguage: {
            "sSearch": "Search:"
        },
    });
}
$("#add_pck_qty").click(function(e){
    var pack_id = $("#pack_id").val();
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
    if($('#selected_packages').val()=='' || $('#selected_packages').val() == null)
    {
        var element = {};
        element[pack_id] = qty;
        $('#selected_packages').val(JSON.stringify(element));    
    }
    else{
        selectedId = $('#selected_packages').val();
        obj = JSON.parse(selectedId);
        obj[pack_id] = qty;
        $('#selected_packages').val(JSON.stringify(obj));
    }
    $("#pack_id").val('');
    $("#qty").val('');
    $("#qty").removeClass('error_border');
    $('label.error').remove();
    
    etin = $("#"+pack_id).find("td:eq(0)").text()
    pack_detail = $("#"+pack_id).find("td:eq(1)").text()
    GetParentSelecedProducts(etin,pack_detail,qty,pack_id);
    $('#'+pack_id).remove();
    $("#selected_packages_qty").modal('hide');
    table.draw(false);

})
GetParentSelecedProducts = (etin,pack_detail,qty,pack_id) =>{
    html = '<tr id="'+pack_id+'"><td>'+etin+'</td><td>'+pack_detail+'</td><td><input type="hidden" name="kit_components['+pack_id+'][components_ETIN]" value="'+etin+'" id="kit_components_etin"><input type="number" class="form-control" name="kit_components['+pack_id+'][qty]" value="'+qty+'" id="kit_components_qty" style="width:55px;padding:0px"></td><td><a href="javascript:void(0)" class="btn btn-danger" onClick="removeProduct('+pack_id+')">Delete</a></td></tr>'
    $("#parent_kit_packaging_selected tbody").append(html);
}
removeProduct = (id) => {
    selectedId = $('#selected_packages').val();
    obj = JSON.parse(selectedId);
    delete obj[id];
    $('#selected_packages').val(JSON.stringify(obj));
    $('#'+id).remove();
    table.draw(false);
}

    function disableScannableBarcodeTextBox(value) {
        if (!value.checked) {
            $("#scannable_barcode").attr("disabled", "disabled");
            $("#scannable_barcode").val("");
        } else {
            $("#scannable_barcode").removeAttr("disabled");
        }
    }

    var productList = null;

    function fetchChannelAndProduct(clientId, append = 1) {
        $.ajax({
			url:'/get_client_channels_and_product/' + clientId,
			method:'GET',
			success:function(response){
				var channels = response.channels;
				var products = response.products;
                productList = products;

                if (append == 1) {
                    appendChannel(channels);
                    appendUnitAndItemFormDescription(products);
                } 
                populateProduct();
			}
		});
    }

    function appendChannel(channelList) {
        var select_elem = document.getElementById('channel_name');
        var options = select_elem.getElementsByTagName('option');
        for (var i = options.length; i--;) {	
            select_elem.removeChild(options[i]);
        }
        
        for (var i = 0; i < channelList.length; i++) {
            let opt = document.createElement("option");
            opt.value = channelList[i].id; 
            opt.innerHTML = channelList[i].channel; 
            select_elem.append(opt); 
        }
    }

    function appendProduct(productList) {
        var select_elem = document.getElementById('product_name');
        var options = select_elem.getElementsByTagName('option');
        for (var i = options.length; i--;) {	
            select_elem.removeChild(options[i]);
        }
        
        for (var i = 0; i < productList.length; i++) {
            let opt = document.createElement("option");
            opt.value = productList[i].id; 
            opt.innerHTML = productList[i].product_name; 
            select_elem.append(opt); 
        }
    }

    function appendUnitAndItemFormDescription(productList) {

        var select_elem = document.getElementById('unit_description');        
        var options = select_elem.getElementsByTagName('option');
        for (var i = options.length; i--;) {	
            select_elem.removeChild(options[i]);
        }
        var array = [];

        let opt = document.createElement("option");
        opt.value = ""; 
        opt.innerHTML = "--Select--";
        select_elem.append(opt); 

        for (var i = 0; i < productList.length; i++) {
            if (array.includes(productList[i].unit_description) 
                || productList[i].unit_description == ''
                || productList[i].unit_description == null) {
                continue
            } 
            opt = document.createElement("option");
            opt.value = productList[i].unit_description; 
            opt.innerHTML = productList[i].unit_description;
            array.push(productList[i].unit_description);
            select_elem.append(opt); 
        }

        array = [];
        select_elem = document.getElementById('item_form_description');
        options = select_elem.getElementsByTagName('option');
        for (var i = options.length; i--;) {	
            select_elem.removeChild(options[i]);
        }

        opt = document.createElement("option");
        opt.value = ""; 
        opt.innerHTML = "--Select--";
        select_elem.append(opt); 
        for (var i = 0; i < productList.length; i++) {
            if (array.includes(productList[i].item_form_description) 
                || productList[i].item_form_description == ''
                || productList[i].item_form_description == null) {
                continue
            } 
            opt = document.createElement("option");
            opt.value = productList[i].item_form_description; 
            opt.innerHTML = productList[i].item_form_description;
            array.push(productList[i].item_form_description);
            select_elem.append(opt); 
        }
    }

    function populateProduct() {

        if (!productList) {
            fetchChannelAndProduct($('#clients_assigned').val(), 0);
        } else {
            var unit_desc = $('#unit_description').val();
            var item_form_desc = $('#item_form_description').val();

            var select_elem = document.getElementById('product_name');
            var options = select_elem.getElementsByTagName('option');
            for (var i = options.length; i--;) {	
                select_elem.removeChild(options[i]);
            }

            for (var i = 0; i < productList.length; i++) {

                if (unit_desc && unit_desc != '' && productList[i].unit_description != unit_desc) {
                    continue;
                }
                
                if (item_form_desc && item_form_desc != '' && productList[i].item_form_description != item_form_desc) {
                    continue;
                }

                let opt = document.createElement("option");
                opt.value = productList[i].id; 
                opt.innerHTML = productList[i].product_name; 
                select_elem.append(opt); 
            }
        }
    }   

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/cranium/kitpackaging/edit.blade.php ENDPATH**/ ?>