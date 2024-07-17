<!DOCTYPE html>
<html>
<head>
</head>
<body style="padding:0px">
    <table id="template_table">
        <thead>
            <tr>
                <td>Warehouse</td>
                <td>ETIN</td>
                <td>UPC</td>
                <td>GTIN</td>
                <td>Product Listing Name</td>
                <td>Location</td>
                <td>Location Type</td>
                <td>Starting Inventory</td>
                <td>Ending Inventory</td>
                <td>Total Change</td>
                <td>Date</td>
                <td>Time</td>
                <td>User</td>
            </tr>  
        </thead>
        <tbody>
            <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($value->warehouse ? $value->warehouse : '-'); ?></td>
                    <td><?php echo e($value->ETIN); ?></td>
                    <td><?php echo e($value->upc); ?></td>
                    <td><?php echo e($value->gtin); ?></td>
                    <td><?php echo e($value->product_listing_name); ?></td>
                    <td><?php echo e($value->location); ?></td>
                    <td><?php echo e($value->type); ?></td>
                    <td><?php echo e($value->starting_qty); ?></td>
                    <td><?php echo e($value->ending_qty); ?></td>
                    <td><?php echo e($value->total_change); ?></td>
                    <td></td>
                    <td></td>
                    <td><?php echo e($value->name); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
        </tbody>
    </table>
</body>
</html><?php /**PATH C:\wamp64\www\cranium_new\resources\views/cranium/reports/export/inventory_adjustment_report_export.blade.php ENDPATH**/ ?>