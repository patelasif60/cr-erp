
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
                    <h6 class="card-title task-title">Create Packaging & Material</h6>
                </div>
                <form method="POST" id="packaging_material_add_form">
                	<?php echo csrf_field(); ?>
                    <input type="hidden" name="supplier_id" value="<?php echo e($supplier->id); ?>">
                    <input type="hidden" name="item_form_description" value="<?php echo e($type); ?>">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="name" class="ul-form__label">ETIN:<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="ETIN" name="ETIN" placeholder="ETIN" value='<?php echo e($newetin); ?>' readonly>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="description" class="ul-form__label">Product Description:<span class="text-danger">*</span></label>
                                        <textarea required name="product_description" id="product_description" cols="10" rows="3" class="form-control" placeholder="Enter Product Description"></textarea>
                                    </div>
                                    <div class="form-group col-md-12">
	                                    <label for="sales_manager" class="ul-form__label">Material Type</label>
	                                    <select class="form-control select2" id="material_type_id" name="material_type_id">
	                                        <option value="">--Select--</option>
	                                        <?php if($materialTypes): ?>
												<?php $__currentLoopData = $materialTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $materialType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($materialType->id); ?>"><?php echo e($materialType->material_type); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
	                                    </select>
                                	</div>
                                    <div class="form-group col-md-12">
                                        <label for="main_point_of_contact" class="ul-form__label">Quantity Per Bundle<span class="text-danger">*</span></label>
                                        <input type="text" required class="form-control" id="quantity_per_bundle" name="quantity_per_bundle" placeholder="Enter Quantity Per Bundle">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="address" class="ul-form__label">Bundle Qty Per Truck Load<span class="text-danger">*</span></label>
                                        <input type="text" required class="form-control" id="bundle_qty_per_truck_load" name="bundle_qty_per_truck_load" placeholder="Enter Bundle Qty Per Truck Load">
                                    </div>                                    
                                    <div class="form-group col-md-12">
	                                    <label for="sales_manager" class="ul-form__label">Product Temperature</label>
	                                    <select class="form-control select2" id="product_temperature" name="product_temperature">
	                                        <option value="">--Select--</option>
                                            <option value="Packaging" selected>Packaging</option>
	                                        <?php if($producttemp): ?>
												<?php $__currentLoopData = $producttemp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($value); ?>"><?php echo e($value); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											<?php endif; ?>
                                            
	                                    </select>
                                	</div>
                                    <div class="form-group col-md-12">
                                        <label for="client_assigned" class="ul-form__label">Client:</label>
                                        <select class="form-control select2" id="client_assigned" name="clients_assigned" onchange="fetchChannelAndProduct(this.value)">
                                            <option value="">--Select--</option>
                                            <?php if($client): ?>
                                                <?php $__currentLoopData = $client; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>                                        
                                        </select>
                                    </div>	                            	                            
                                    <div class="form-group col-md-12">
                                        <label for="sales_manager" class="ul-form__label">Channels</label>
                                        <select id="channel_name" class="form-control select2" name="channel_ids[]" multiple>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="unit_description" class="ul-form__label">Unit Description</label>
                                        <select onchange="populateProduct()" id="unit_description" class="form-control select2" name="unit_desc">
                                            <option value="">--Select--</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="item_form_description" class="ul-form__label">Item Form Description</label>
                                        <select onchange="populateProduct()" id="item_form_description" class="form-control select2" name="item_form_desc">
                                            <option value="">--Select--</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="sales_manager" class="ul-form__label">Products</label>
                                        <select id="product_name" class="form-control select2" name="product_ids[]" multiple>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group col-md-12">
                                    <label for="weight" class="ul-form__label">Weight (lbs):</label>
                                    <input type="number" class="form-control" id="weight" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="weight" placeholder="Enter Weight">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="external_length" class="ul-form__label">External Length:</label>
                                    <input type="number" class="form-control" id="external_length" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="external_length" placeholder="Enter External Length">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="external_width" class="ul-form__label">External Width:</label>
                                    <input type="number" class="form-control" id="external_width" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="external_width" placeholder="Enter External Width">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="external_height" class="ul-form__label">External Height:</label>
                                    <input type="number" class="form-control" id="external_height" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="external_height" placeholder="Enter External Height">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="internal_length" class="ul-form__label">Internal Length:</label>
                                    <input type="number" class="form-control" id="internal_length" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="internal_length" placeholder="Enter Internal Length">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="internal_width" class="ul-form__label">Internal Width:</label>
                                    <input type="number" class="form-control" id="internal_width" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="internal_width" placeholder="Enter Internal Width">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="internal_height" class="ul-form__label">Internal Height:</label>
                                    <input type="number" class="form-control" id="internal_height" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$"  name="internal_height" placeholder="Enter Internal Height">
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
                                    <input type="number" class="form-control" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" id="cost" name="cost" placeholder="Enter Cost">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="new_cost" class="ul-form__label">New Cost:</label>
                                    <input type="number" class="form-control" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" id="new_cost" name="new_cost" placeholder="Enter New Cost">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="new_cost_date" class="ul-form__label">New Cost Date:</label>
                                     <input type="text" class="form-control flatpickr" id="new_cost_date" name="new_cost_date">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="acquisition_cost" class="ul-form__label">Acquisition Cost:</label>
                                    <input type="number" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" name="acquisition_cost" id="acquisition_cost" class="form-control" placeholder="Enter Acquisition Cost">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="capacity_cubic" class="ul-form__label">Capacity Cubic:</label>
                                    <input type="number" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" name="capacity_cubic" id="capacity_cubic" class="form-control" placeholder="Enter Capacity Cubic">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="phone" class="ul-form__label">Supplier Product Number</label>
                                    <input type="text" class="form-control" id="supplier_product_number" name="supplier_product_number" placeholder="Enter Supplier Product Number">
                                </div>
                                <!-- 
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
													<td><input type="checkbox" calss="js-warehouseval" name="warehouse[]" value="<?php echo e($warehouses); ?>"></td>
													<td></td>
												</tr>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										<?php endif; ?>
                                    </table>
	                            </div>
	                            <div class="form-group col-md-12">
	                                <label for="sales_manager" class="ul-form__label">Bulk Price:</label>
	                                <input type="number" class="form-control" min="0.001" step="0.001" pattern="^\d+(?:\.\d{1,3})?$" name="bluck_price" id="bluck_price" placeholder="Enetr Bulk Price">
	                            </div>
                                <div class="form-group col-md-12 mt-3">
                                    <label for="has_barcode" class="ul-form__label">Has Scannable Barcode:</label>
                                    <input type="checkbox" id="has_barcode" name="has_barcode" onchange="disableScannableBarcodeTextBox(this)">
                                </div>  
                                <div class="form-group col-md-12">
                                    <label for="email" class="ul-form__label">Scannable Barcode:</label>
                                    <input class="form-control" id="scannable_barcode" name="scannable_barcode" placeholder="Enter Scannable Barcode">
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
                                                <input type="number" class="form-control" id="<?php echo e($wh_lower); ?>_td_<?php echo e($td); ?>" name="<?php echo e($wh_lower); ?>_td_<?php echo e($td); ?>" placeholder="Max Item for Transit Day <?php echo e($td); ?>">
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                                                      
                                    </div>  
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="mc-footer">
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                    <a href="<?php echo e(route('suppliers.index')); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end of col -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<script src="<?php echo e(asset('assets/js/validation/jquery.validate.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/validation/additional-methods.min.js')); ?>"></script>
<script type="text/javascript">

    disableScannableBarcodeTextBox($('#has_barcode').val());

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
            fetchChannelAndProduct($('#client_assigned').val(), 0);
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

    $('.LobsDrop ').on('click','.lobs_cards',function(e){
        var dropped_lobs = $(this).attr('id');
        var lobs_assigned = $("#clients_assigned").val();
        var lobs_array = [];
        if(lobs_assigned == ''){
            lobs_array.push(dropped_lobs);
            $("#clients_assigned").val(lobs_array.join(','));
        }else{
            lobs_array = lobs_assigned.split(',');
            lobs_array.push(dropped_lobs);
            $("#clients_assigned").val(lobs_array.join(','));
        }

        $('.LobsDropAssigned').append(this);
    });

    $(".LobsDropAssigned").on('click','.lobs_cards',function(e){
        var dropped_lobs = $(this).attr('id');
        console.log(dropped_lobs);
        var lobs_assigned = $("#clients_assigned").val();
        var lobs_array = [];
        if(lobs_assigned == ''){

        }else{
            lobs_array = lobs_assigned.split(',');
            lobs_array.splice($.inArray(dropped_lobs, lobs_array), 1);
            $("#clients_assigned").val(lobs_array.join(','));
        }
        $('.LobsDrop').append(this);
    });
	$("#packaging_material_add_form").validate({
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
		var form_cust = $('#packaging_material_add_form')[0];
		let form1 = new FormData(form_cust);
        console.log(form1);
		$.ajax({
			type: "POST",
			url: '<?php echo e(route('addpackagematerialstore')); ?>',
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/suppliers/packaging_material/create.blade.php ENDPATH**/ ?>