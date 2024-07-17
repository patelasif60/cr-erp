
<?php $__env->startSection('page-css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">
    <style>
        .flatpickr-wrapper{
            width:100% !important;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
    <?php $required_span = '<span class="text-danger">*</span>' ?>
    <div class="breadcrumb">
        <h1>Purchasing</h1>
        <ul>
            <li><a href="">Purchasing</a></li>
            <li>Edit</li>
            <?php if(isset($row)): ?>
                <li><a href="<?php echo e(route('suppliers.index')); ?>">Supplier</a></li>
                <li><a href="<?php echo e(route('suppliers.edit',$row->id)); ?>"><?php echo e($row->name); ?></a></li>
            <?php else: ?>
                <li><a href="<?php echo e(route('clients.index')); ?>">Client</a></li>
                <li><a href="<?php echo e(route('clients.edit',$c_row->id)); ?>"><?php echo e($c_row->company_name); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="col-md-6">
        <div id="error_container"></div>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Edit Purchase Order</h6>
                    <ul>
                        <li>Warehouse: <label id='wh_name'>NA</label></li>
                        <li>PO # <label id='po_num'><?php echo e(isset($ps->order) ? $ps->order : 'NA'); ?></label></li>
                        <li>Frozen Weight: <label id='frz_wt'>NA</label></li>
                        <li>Dry Weight: <label id='dry_wt'>NA</label></li>
                        <li>Refrigirated Weight: <label id='ref_wt'>NA</label></li>
                    </ul>
                </div>
                <form action="#" id="new_po">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="supplier_product_package_type" class="ul-form__label">Warehouse:<?php echo $required_span; ?></label>
                                    <select class="form-control " id="warehouse_id" name="warehouse" onchange="getProductList()">
                                        <option value="">--Select--</option>
                                        <?php if($warehouses): ?>
                                            <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>                       
                                                <option value="<?php echo e($warehouse->id); ?>" <?php if(isset($ps)){ if($ps->warehouse_id == $warehouse->id) echo "selected";}?> ><?php echo e($warehouse->warehouses); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Order Date:<?php echo $required_span; ?></label>
                                    <input type="text" class="form-control date_picker" id="order_date" name="order_date" placeholder="Enter Order Date" value="<?php echo e($ps->purchasing_asn_date); ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Delivery Date:<?php echo $required_span; ?></label>
                                    <input type="text" class="form-control date_picker" id="delivery_date" name="delivery_date" placeholder="Enter Delivery Date" value="<?php echo e($ps->delivery_date); ?>"/>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Delivery Fees:</label>
                                    <input type="text" class="form-control" id="delivery_fees" name="delivery_fees" placeholder="Enter Delivery Fees" value="<?php echo e($ps->delivery_inbound_fees); ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Freight Fees:</label>
                                    <input type="text" class="form-control" id="freight_fees" name="freight_fees" placeholder="Enter Freight Fees" value="<?php echo e($ps->freight_shipping_charge); ?>"/>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="name" class="ul-form__label">Surcharge Fees:</label>
                                    <input type="text" class="form-control" id="surcharge" name="surcharge" placeholder="Enter Surcharge" value="<?php echo e($ps->surcharge_1); ?>"/>
                                </div>
                            </div>                            
                            <div class="col-lg-12">
                                <table class="table table-border" id="purchase_order_products">
                                    <thead>
                                        <tr>
                                            <th>ETIN</th>
                                            <th>Product Number</th>
                                            <th>Product Listing Name</th>
                                            <th>Status</th>
                                            <th>Lead Time</th>
                                            <th>Product Availability</th>
                                            <th>On hand Qty</th>
                                            <th>On Order Qty</th>
                                            <th>Weeks Worth QTY</th>
                                            <th>Min Order QTY</th>
                                            <th>Suggested Order QTY</th>
                                            <th>Order QTY</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button type="button" class="btn  btn-primary m-1" id="saveAsDraft" <?php if(isset($ps)){ if($ps->po_status != 'Pending') echo "disabled";}?>>Save Draft PO</button>
                                <button type="button" class="btn  btn-primary m-1" id="submitPo">Submit Purchase Order</button>
                                <?php if(isset($row)): ?>
                                    <a href="<?php echo e(route('suppliers.edit', $row->id)); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('clients.edit', $c_row->id)); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="card-footer bg-transparent">
                        <div class="mc-footer">
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="btn  btn-primary m-1">Submit</button>
                                    <a href="<?php echo e(route('clients.index')); ?>" class="btn btn-outline-secondary m-1">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </form>

            </div>
        </div>
    </div>
    <!-- end of col -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-js'); ?>
    <script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
    <script>
       
        $(document).ready(function () {
            $('#cuttoff_time').flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
            });
            $('.date_picker').flatpickr({
        static: true,
        enableTime: false,
        dateFormat: "Y-m-d",
    });
            getProductList(true);                     
        });
 
        function getProductList(onload = false) {
            var wh = document.getElementById("warehouse_id");
            var warehouseId = wh.value;
            document.getElementById('wh_name').innerText = wh.children.item(wh.selectedIndex).innerText;
            PurchaseOrderProducts(warehouseId, onload);
            setTimeout(() => {
                calculateWeights(); 
            }, 3000);
        }

        function calculateWeights() {
            $('div#preloader').show();
            var values = $('#purchase_order_products').DataTable().data().toArray();
            var dryWeight = 0, frozenWeight = 0, refWeight = 0;
            for (i in values) {
                var weight = +values[i].weight
                var temp =  String(values[i].temp)
                var orderQty = +values[i].order_qty
                if (temp === 'dry') {
                    dryWeight += (weight * orderQty)
                } else if (temp === 'frozen') {
                    frozenWeight += (weight * orderQty)
                } else if (temp === 'refrigirated') {
                    refWeight += (weight * orderQty)
                }
            }
            document.getElementById('frz_wt').innerText = frozenWeight + ' lbs'
            document.getElementById('dry_wt').innerText = dryWeight + ' lbs'
            document.getElementById('ref_wt').innerText = refWeight + ' lbs'
            $('div#preloader').hide();
        }
 
        function PurchaseOrderProducts(warehouseId, onload) {
            table1 = $('#purchase_order_products').DataTable({
                // dom:"Bfrtip",
                paging:   true,
                destroy: true,
                responsive: false,
                processing: true,
                serverSide: false,
                autoWidth: false,
                colReorder: true,
                scrollX: true,
                lengthMenu: [[25, 100, -1], [25, 100, "All"]],
                pageLength: 25,

                ajax:{
                        url: onload ? '<?php echo e(route('datatable.SavedPurchaseOrderProducts')); ?>' : '<?php echo e(route('datatable.PurchaseOrderProducts')); ?>',
                        method: 'GET',
                        data: function(d) {
                            d['supplier'] = '<?php echo e(isset($row)  ? $row->id : ''); ?>';
                            d['client'] = '<?php echo e(isset($c_row)  ? $c_row->id : ''); ?>';
                            d['po'] = '<?php echo e($ps->order); ?>'
                            d['warehouse_id'] = warehouseId;
                        }
                    },
                columns: [
                    {data:'ETIN' , name:'ETIN'},
                    {data:'supplier_product_number' , name:'supplier_product_number',defaultContent:'-'},
                    {data:'product_listing_name' , name:'product_listing_name'},
                    {data:'status' , name:'status',defaultContent:'-'},
                    {data:'lead_time' , name:'lead_time',defaultContent:'-'},                    
                    {data:'product_availability', name: 'product_availability', defaultContent: '-'},
                    {data:'on_hand_qty' , name:'on_hand_qty',defaultContent:'0'},
                    {data:'on_order_qty' , name:'on_order_qty',defaultContent:'0'},
                    {data:'week_worth_qty' , name:'week_worth_qty',defaultContent:'0'},
                    {data:'min_order_qty' , name:'min_order_qty',defaultContent:'0'},
                    {data:'suggested_order_qty' , name:'suggested_order_qty',defaultContent:'0'},
                    {data:'order_qty' , name:'order_qty',defaultContent:'0', 
                                render: function (data, type, row) {
                                    return '<input class="form-control trackInput" id="order_qty" name="order_qty" type="text"  value = ' + row.order_qty + '  />';}
                    },
                    {data:'temp' , name:'temp',defaultContent:'0', visible: false, searchable: false},
                    {data:'weight' , name:'weight',defaultContent:'0', visible: false, searchable: false},
                ],
                "drawCallback": function( settings ) {
                    $(".trackInput").on("change",function(){
                        var $row = $(this).parents("tr");
                        var rowData = table1.row($row).data();
                        rowData.order_qty = $(this).val();
                        calculateWeights();
                    });
                }
            });        
        }
 
        //Save as Draft
        $('#saveAsDraft').click(function (e){            
            $('input').attr('required', false);
                $('div#preloader').show();
            e.preventDefault();
            var form_cust = $('#new_po')[0];
            let form1 = new FormData(form_cust);

            var warehouseId = document.getElementById("warehouse_id").value;
            form1.append('warehouse_id', warehouseId);
            form1.append('supplier_id', '<?php echo e(isset($row) ? $row->id : ''); ?>');
            form1.append('client_id', '<?php echo e(isset($c_row) ? $c_row->id : ''); ?>');
            form1.append('summary_id', '<?php echo e($ps->id); ?>')

            var url = '/purchase_order/saveAsDraft';

            var table = document.getElementById('purchase_order_products');
            var items = []

            var values = $('#purchase_order_products').DataTable().data().toArray();
            for (i in values) {
                items.push({
                    'etin': values[i].ETIN,
                    'product_number': values[i].supplier_product_number,
                    'product_listing_name': values[i].product_listing_name,
                    'status': values[i].status,
                    'on_hand_qty': values[i].on_hand_qty,
                    'on_order_qty': values[i].on_order_qty,
                    'week_worth_qty': values[i].week_worth_qty,
                    'min_order_qty': values[i].min_order_qty,
                    'suggested_order_qty': values[i].suggested_order_qty,
                    'order_qty': values[i].order_qty,
                })
            }
            form1.append('items', JSON.stringify(items));

            $('div#preloader').hide();
            $.ajax({
                type: "POST",
                url: url,
                data: form1,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $('div#preloader').hide();
                    if(response.error == 0) {
                        toastr.success(response.msg);
                        window.location.href = response.url
                    } else {
                        toastr.error(response.msg);
                    }
                },
                error: function(data){
                    $(".submit").attr("disabled", false);
                    $('div#preloader').hide();
                    var errors = data.responseJSON;
                    $("#error_container").html('');
                    $('label[class=error]').remove();
                    // $.each( errors.errors.original, function( key, value ) {
                    //     var ele = "#"+key;
                    //     $(ele).addClass('error_border');
                    //     $('<label class="error">'+ value +'</label>').insertAfter(ele);
                    //     $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
                    //     toastr.error(value);
                    // });

                    $.each( errors.errors, function( key, value ) {
                        var ele = "#"+key;
                        $(ele).addClass('error');
                        $('<label class="error">'+ value +'</label>').insertAfter(ele);
                    });
                }
            });
        });
 
        $('#submitPo').click(function (e){            
            $('input').attr('required', false);
            $('div#preloader').show();
            e.preventDefault();
            var form_cust = $('#new_po')[0];
            let form1 = new FormData(form_cust);

            var warehouseId = document.getElementById("warehouse_id").value;
            form1.append('warehouse_id', warehouseId);
            form1.append('supplier_id', '<?php echo e(isset($row) ? $row->id : ''); ?>');
            form1.append('client_id', '<?php echo e(isset($c_row) ? $c_row->id : ''); ?>');
            form1.append('summary_id', '<?php echo e($ps->id); ?>')

            var url = '/purchase_order/submit_po';

            var table = document.getElementById('purchase_order_products');
            var items = [];

            var values = $('#purchase_order_products').DataTable().data().toArray();
            for (i in values) {
                items.push({
                    'etin': values[i].ETIN,
                    'product_number': values[i].supplier_product_number,
                    'product_listing_name': values[i].product_listing_name,
                    'status': values[i].status,
                    'lead': values[i].lead_time,
                    'product_availability': values[i].product_availability,
                    'on_hand_qty': String(values[i].on_hand_qty),
                    'on_order_qty': String(values[i].on_order_qty),
                    'week_worth_qty': String(values[i].week_worth_qty),
                    'min_order_qty': String(values[i].min_order_qty),
                    'suggested_order_qty': String(values[i].suggested_order_qty),
                    'order_qty': String(values[i].order_qty),
                })
            }
            console.log(items);
            form1.append('items', JSON.stringify(items));

            $('div#preloader').hide();
            $.ajax({
                type: "POST",
                url: url,
                data: form1,
                processData: false,
                contentType: false,
                success: function( response ) {
                    $('div#preloader').hide();
                    if(response.error == 0) {
                        toastr.success(response.msg);
                        window.location.href = response.url
                    } else {
                        toastr.error(response.msg);
                    }
                },
                error: function(data){
                    $(".submit").attr("disabled", false);
                    $('div#preloader').hide();
                    var errors = data.responseJSON;
                    $("#error_container").html('');
                    $.each( errors.errors.original, function( key, value ) {
                        var ele = "#"+key;
                        $(ele).addClass('error_border');
                        $('<label class="error">'+ value +'</label>').insertAfter(ele);
                        // $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
                        toastr.error(value);
                    });
                    $.each( errors.errors_item, function( key, value ) {
                        $("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
                    });
                }
            });
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\cranium_new\resources\views/purchase_order/edit.blade.php ENDPATH**/ ?>