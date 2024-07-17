<link rel="stylesheet" href="<?php echo e(asset('assets/styles/vendor/datatables.min.css')); ?>">     
<div class="modal-dialog mt-5 modal-lg" style="min-width: 50%">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header bg-light">
            <h5>View Order # <?php echo e($order_number); ?> History</h5>
            <!--Close/Cross Button-->
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div> 
        <div class="modal-body">
            <table class="table table-bordered text-center datatable" id="order_details">
                <thead class="thead-dark">
                    <tr>
                        <th>Order #</th>
                        <th>Sub Order #</th>
                        <th>Title</th>
                        <th>Detail</th>      
                        <th>User</th>                   
                        <th>TimeStamp</th>                         
                    </tr>
                </thead>
                <tbody>
                    <?php if($result): ?>
                        <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($row->etailer_order_number); ?></td>
                                <td><?php echo e($row->sub_order_number); ?></td>
                                <td><?php echo e($row->action); ?></td>
                                <td><?php echo e($row->details); ?></td>
                                <td><?php echo e($row->user_name); ?></td>
                                <td><?php echo e(date('m/d/Y h:i:s A',strtotime($row->created_at))); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="<?php echo e(asset('assets/js/vendor/datatables.min.js')); ?>"></script>
<script>
    $('#order_details').DataTable({
        aaSorting: []
    });
</script>
<?php /**PATH C:\wamp64\www\cranium_new\resources\views/orders/view_order_history.blade.php ENDPATH**/ ?>