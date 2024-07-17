<!DOCTYPE html>
<html>
<body style="padding:0px">
    <table id="template_table">
        <thead>
            <tr>	
                <td>ETIN</td>
                <td>UPC</td>
                <td>GTIN</td>
                <td>Product Listing Name</td>
                <?php if($request->warehouseId == 'NV' || $request->warehouseId == ''): ?>
                <td>NV Total Quantity</td>
                <td>NV Orderable Quantity</td>
                <td>NV Fulfillable Quantity</td>
                <td>NV On Order Quantity</td>
                <td>NV Inbound Quantity</td>
                <?php endif; ?>
                <?php if($request->warehouseId == 'OKC' || $request->warehouseId == ''): ?>
                <td>OK Total Quantity</td>
                <td>OK Orderable Quantity</td>
                <td>OK Fulfillable Quantity</td>
                <td>OK On Order Quantity</td>
                <td>OK Inbound Quantity</td>
                <?php endif; ?>
                <?php if($request->warehouseId == 'WI' || $request->warehouseId == ''): ?>
                <td>WI Total Quantity</td>
                <td>WI Orderable Quantity</td>
                <td>WI Fulfillable Quantity</td>
                <td>WI On Order Quantity</td>
                <td>WI Inbound Quantity</td>
                <?php endif; ?>
                <?php if($request->warehouseId == 'PA' || $request->warehouseId == ''): ?>
                <td>PA Total Quantity</td>
                <td>PA Orderable Quantity</td>
                <td>PA Fulfillable Quantity</td>
                <td>PA On Order Quantity</td>
                <td>PA Inbound Quantity</td>
                <?php endif; ?>
            </tr>  
        </thead>
        <tbody>
            <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($value->ETIN); ?></td>
                    <td><?php echo e($value->product ? $value->product->upc : ''); ?></td>
                    <td><?php echo e($value->product ? $value->product->gtin : ''); ?></td>
                    <td><?php echo e($value->product ? $value->product->product_listing_name: ''); ?></td>
                    <?php if($request->warehouseId == 'NV' || $request->warehouseId == ''): ?>
                    <td><?php echo e($value->nv_qty > 0 ? $value->nv_qty  : 0); ?></td>
                    <td><?php echo e($value->nv_orderable_qty > 0 ? $value->nv_orderable_qty : 0); ?></td>
                    <td><?php echo e($value->nv_fulfilled_qty > 0 ? $value->nv_fulfilled_qty : 0); ?></td>
                    <td><?php echo e($value->nv_open_order_qty > 0 ? $value->nv_open_order_qty : 0); ?></td>
                    <td><?php echo e($value->nv_each_qty > 0 ? $value->nv_each_qty : 0); ?></td>
                    <?php endif; ?>
                    <?php if($request->warehouseId == 'OKC' || $request->warehouseId == ''): ?>
                    <td><?php echo e($value->okc_qty > 0 ? $value->okc_qty : 0); ?></td>
                    <td><?php echo e($value->okc_orderable_qty > 0 ? $value->okc_orderable_qty : 0); ?></td>
                    <td><?php echo e($value->okc_fulfilled_qty > 0 ? $value->okc_fulfilled_qty : 0); ?></td>
                    <td><?php echo e($value->okc_open_order_qty > 0 ? $value->okc_open_order_qty : 0); ?></td>
                    <td><?php echo e($value->okc_each_qty > 0 ? $value->okc_each_qty : 0); ?></td>
                    <?php endif; ?>
                    <?php if($request->warehouseId == 'WI' || $request->warehouseId == ''): ?>
                    <td><?php echo e(floatval($value->wi_qty) > 0 ? $value->wi_qty : 0); ?></td>
                    <td><?php echo e($value->wi_orderable_qty > 0 ? $value->wi_orderable_qty : 0); ?></td>
                    <td><?php echo e($value->wi_fulfilled_qty > 0 ? $value->wi_fulfilled_qty: 0); ?></td>
                    <td><?php echo e($value->wi_open_order_qty > 0 ? $value->wi_open_order_qty : 0); ?></td>
                    <td><?php echo e($value->wi_each_qty > 0 ? $value->wi_each_qty : 0); ?></td>
                    <?php endif; ?>
                    <?php if($request->warehouseId == 'PA' || $request->warehouseId == ''): ?>
                    <td><?php echo e($value->pa_qty > 0 ? $value->pa_qty : 0); ?></td>
                    <td><?php echo e($value->pa_orderable_qty > 0 ? $value->pa_orderable_qty : 0); ?></td>
                    <td><?php echo e($value->pa_fulfilled_qty > 0 ? $value->pa_fulfilled_qty : 0); ?></td>
                    <td><?php echo e($value->pa_open_order_qty > 0 ? $value->pa_open_order_qty : 0); ?></td>
                    <td><?php echo e($value->pa_each_qty > 0 ? $value->pa_each_qty : 0); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
        </tbody>
    </table>
</body>
</html><?php /**PATH C:\wamp64\www\cranium_new\resources\views/cranium/reports/export/inventory_report_export.blade.php ENDPATH**/ ?>