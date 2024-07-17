
<?php $__env->startSection('page-css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main-content'); ?>
<div class="breadcrumb">
    <h1>Product Inventory</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-md-12 mb-4">
        <div class="card text-left">
        	<!-- <div class="card-header text-right bg-transparent">
                <a href="javascript:void(0);" class="btn btn-primary btn-md m-1" onClick="openModal()"><i class="i-Add text-white mr-2"></i>Add Product Inventory</a>
            </div> -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="inventory_table" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ETIN</th>
                                <th>WI QtY</th>
                                <th>WI Each Qty</th>
                                <th>WI Orderable Qty</th>
                                <th>WI Fulfillable Qty</th>
                                <th>WI Open Order Qty</th>
                                <th>PA QtY</th>
                                <th>PA Each Qty</th>
                                <th>PA Orderable Qty</th>
                                <th>PA Fulfillable Qty</th>
                                <th>PA Open Order Qty</th>
                                <th>NV QtY</th>
                                <th>NV Each Qty</th>
                                <th>NV Orderable Qty</th>
                                <th>NV Fulfillable Qty</th>
                                <th>NV Open Order Qty</th>
                                <th>OKC QtY</th>
                                <th>OKC Each Qty</th>
                                <th>OKC Orderable Qty</th>
                                <th>OKC Fulfillable Qty</th>
                                <th>OKC Open Order Qty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                    </table>
                    <a href="<?php echo e(route('productinventory.exportInv')); ?>" class="btn btn-primary">Export CSV</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="MyModal" tabindex="-1" role="dialog" aria-labelledby="MyModal" aria-hidden="true">
    <div class="modal-dialog">
        <!--Modal Content-->
        <div class="modal-content">
            <!-- Modal Header-->
            <div class="modal-header" style="background-color:#fff;">
                <h3 id="hmodelHeader"></h3>
                <!--Close/Cross Button-->
                 <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
            </div> 
            <form  method="POST" data-form="add" action="javascript:void(0)" id="add_form" >
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="group_name" class="ul-form__label">ETIN</label>
                            <select name="ETIN" id="etin" class="form-control">
                                <option value="">Select ETIN</option>
                                <?php $__currentLoopData = $etin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$row_etin2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($row_etin2); ?>"><?php echo e($row_etin2); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="warehouses_assigned" class="ul-form__label">Warehouse</label>
                            <table class="table">
                                <?php if($warehouses): ?>
                                    <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($warehouse); ?></td>
                                            <td><input type="checkbox" name="warehouses[]" value="<?php echo e($key); ?>"></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </table>
                        </div>
                        <!-- <div class="form-group col-md-12">
                            <label for="inventory" class="ul-form__label">Inventory</label>
                            <input class="form-control" type="number" name="inventory" id="inventory" min="0" step="1">
                        </div> -->
                    </div> 
                </div>
                <div class="modal-footer"> 
                    <button type="submit" class="btn btn-primary btn-txt">Add</button> 
                    <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="MyModalEdit" tabindex="-1" role="dialog" aria-labelledby="MyModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="editMode"></div>
    </div>
</div>

<div class="modal fade" id="ChildModal" tabindex="-1" role="dialog" aria-labelledby="MyModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
             <div class="modal-header" style="background-color:#fff;">
                <h3 id="hmodelHeader"></h3>
                <!--Close/Cross Button-->
                 <button type="button" class="close reset-text" data-dismiss="modal">&times;</button>
            </div> 
            <div class="card-body">
                <div class="table-responsive">
                    <table id="inventory_child_table" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ETIN</th>
                                <th>WI QtY</th>
                                <th>PA QtY</th>
                                <th>NV QtY</th>
                                <th>OKC QtY</th>
                                <th>WI Each Qty</th>
                                <th>PA Each Qty</th>
                                <th>NV Each Qty</th>
                                <th>OKC Each Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
<script src="<?php echo e(asset('assets/js/validation/jquery.validate.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/vendor/sweetalert2.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/sweetalert.script.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/validation/additional-methods.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
<script>
   $(document).ready(function () {
        var table_html = '';
        var table_html_td = '';
        var i = 1;
        var dt = $('#inventory_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: false,
            autoWidth: false,
            ordering: false,
            
            ajax: '<?php echo route('productinventory.productinventorylist'); ?>',
            columns: [
                { data: 'ETIN', name: 'ETIN' },
                { data: 'wi_qty', name: 'wi_qty' },
                { data: 'wi_each_qty', name: 'wi_each_qty' },
                { data: 'wi_orderable_qty', name: 'wi_orderable_qty' },
                { data: 'wi_fulfilled_qty', name: 'wi_fulfilled_qty' },
                { data: 'wi_open_order_qty', name: 'wi_open_order_qty' },
                { data: 'pa_qty', name: 'PA_qty' },
                { data: 'pa_each_qty', name: 'pa_each_qty' },
                { data: 'pa_orderable_qty', name: 'pa_orderable_qty' },
                { data: 'pa_fulfilled_qty', name: 'pa_fulfilled_qty' },
                { data: 'pa_open_order_qty', name: 'pa_open_order_qty' },
                { data: 'nv_qty', name: 'nv_qty' },
                { data: 'nv_each_qty', name: 'nv_each_qty' },
                { data: 'nv_orderable_qty', name: 'nv_orderable_qty' },
                { data: 'nv_fulfilled_qty', name: 'nv_fulfilled_qty' },
                { data: 'nv_open_order_qty', name: 'nv_open_order_qty' },
                { data: 'okc_qty', name: 'okc_qty' },
                { data: 'okc_each_qty', name: 'okc_each_qty' },
                { data: 'okc_orderable_qty', name: 'okc_orderable_qty' },
                { data: 'okc_fulfilled_qty', name: 'okc_fulfilled_qty' },
                { data: 'okc_open_order_qty', name: 'okc_open_order_qty' },
                { data: 'action', name: 'action', searchable: false }
            ]
        });
   });
   function openModal(){
        $('#MyModal').modal('show');
        $('#add_form').attr('data-form','add');
        $('#hmodelHeader').text('Add Product Inventory');
        $('.btn-txt').text('Add');

    }
    function openEditModal(id){
        var url = '<?php echo e(route('productinventory.edit')); ?>';
        $.ajax({
            type: "POST",
            url: url,
            data: {'id':id}, 
            success: function( response ) {
               $("#editMode").html(response);
               $('#MyModalEdit').modal('show');
            },
        })
    }
    $('body').on('click', '.edit-form', function() {
        url = '<?php echo e(route('productinventory.update')); ?>';
        $(".submit").attr("disabled", true);
        var form_cust = $('#edit_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: url,
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error == 0){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        location.reload();
                    },2000);
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
    })
    $("#add_form").validate({
        submitHandler(form){
            var formtype = $(form).attr('data-form');
            $(".submit").attr("disabled", true);
            var form_cust = $('#add_form')[0]; 
            let form1 = new FormData(form_cust);
            var url = '<?php echo e(route('productinventory.store')); ?>';
            if(formtype == 'edit'){
                url = '<?php echo e(route('productinventory.update')); ?>'
            }
            $.ajax({
                type: "POST",
                url: url,
                data: form1, 
                processData: false,
                contentType: false,
                success: function( response ) {
                    if(response.error == 0){
                        toastr.success(response.msg);
                        setTimeout(function(){
                            location.reload();
                        },2000);
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
    function openChildModal(ETIN){
       var table_html = '';
        var table_html_td = '';
        var i = 1;
        var dt = $('#inventory_child_table').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ordering: false,
            ajax:{
                    url: '<?php echo e(route('productinventory.chilproductinventorylist')); ?>',
                    method:'GET',
                    data: {
                        ETIN:ETIN,
                    }
                },
            columns: [
                { data: 'ETIN', name: 'ETIN' },
                { data: 'WI_qty', name: 'WI_qty' },
                { data: 'PA_qty', name: 'PA_qty' },
                { data: 'NV_qty', name: 'NV_qty' },
                { data: 'OKC_qty', name: 'OKC_qty' },
                { data: 'wi_each_qty', name: 'wi_each_qty' },
                { data: 'pa_each_qty', name: 'pa_each_qty' },
                { data: 'NV_each_qty', name: 'NV_each_qty' },
                { data: 'OKC_each_qty', name: 'OKC_each_qty' },
            ]
        });
        $('#ChildModal').modal('show');
        
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/product_inventory/index.blade.php ENDPATH**/ ?>