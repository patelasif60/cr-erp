     
<div class="modal-dialog mt-5 modal-lg" style="min-width: 80%">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header bg-light">
            <h5>Search Text: <strong><?php echo e($search_text); ?></strong></h5>
            <!--Close/Cross Button-->
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div> 
        <div class="row modal-body">
            <div class="col-md-12">
                <div class="card o-hidden mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-2">
                                <h3 class="w-50 float-left card-title m-0">Orders</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 row" style="">
                            <table class="table table-bordered text-center" id="orders_search_table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Order Date</th>
                                        <th>e-tailer Order Number</th>
                                        <th>Client</th>
                                        <th>Address</th>
                                        <th>Ship By</th>                                            
                                        <th>Order Status</th>
                                        <th>Tracking Number</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($orders && count($orders) > 0): ?>
                                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($res['created_at']); ?></td>
                                                <td><?php echo e($res['etailer_order_number']); ?></td>
                                                <td><?php echo e($res['client_name']); ?></td>
                                                <td>
                                                    <?php echo e($res['ship_to_name'] 
                                                        . ', ' . $res['ship_to_address1']
                                                        . ', ' . $res['ship_to_city']
                                                        . ', ' . $res['ship_to_state']
                                                        . ', ' . $res['ship_to_zip']); ?>

                                                </td>
                                                <td><?php echo e($res['ship_by_date']); ?></td>
                                                <td><?php echo e($res['order_status_name']); ?></td>
                                                <td>
                                                    <?php if(isset($res['track_number'])): ?>
                                                        <?php echo e($res['track_number']); ?>

                                                    <?php elseif(array_key_exists($res['etailer_order_number'], $t_num)): ?>
                                                        <?php echo e($t_num[$res['etailer_order_number']]); ?>

                                                    <?php else: ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo e(route('orders.view',$res['id'])); ?>" target="_blank" class="edit btn btn-primary btn-sm">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9">No Orders found</td>
                                        </tr>
                                    <?php endif; ?>                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row modal-body">
            <div class="col-md-12">
                <div class="card o-hidden mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-2">
                                <h3 class="w-50 float-left card-title m-0">Products</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 row" style="">
                            <table class="table table-bordered text-center" id="products_search_table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ETIN</th>
                                        <th>Product Listing Name</th>
                                        <th>Product Type</th>
                                        <th>Product Category</th>
                                        <th>UPC</th>
                                        <th>GTIN</th> 
                                        <th>Action</th>                                                                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($products && count($products) > 0): ?>
                                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($res['ETIN']); ?></td>
                                                <td><?php echo e($res['product_listing_name']); ?></td>
                                                <td><?php echo e($res['product_type']); ?></td>
                                                <td><?php echo e($res['product_category']); ?></td>
                                                <td><?php echo e($res['upc']); ?></td>
                                                <td><?php echo e($res['gtin']); ?></td>
                                                
                                                <?php if($res->item_form_description == 'Kit'): ?>
                                                    <td>
                                                        <a href="<?php echo e(route('kits.edit',$res->id)); ?>" target="_blank" class="edit btn btn-primary btn-sm">
                                                            Edit Product
                                                        </a>
                                                    </td>
                                                <?php else: ?>
                                                    <td>
                                                        <a href="<?php echo e(route('editmasterproduct',$res->id)); ?>" target="_blank" class="edit btn btn-primary btn-sm">
                                                            Edit Product
                                                        </a>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7">No Products found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
<script type="text/javascript">
    $('#orders_search_table').DataTable();
    $('#products_search_table').DataTable();
</script>
<?php /**PATH C:\wamp64\www\cranium_new\resources\views/layouts/products-order.blade.php ENDPATH**/ ?>