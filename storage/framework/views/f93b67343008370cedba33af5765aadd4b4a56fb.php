<!DOCTYPE html>
<html>

<body>
    <table>
        <thead>
            <tr>
                <td>Order No</td>
                <td>Customer Order No</td>
                <td>Client</td>
                <td>Warehouse</td>
                <td>Order Date</td>
                <td>Carrier</td>	
                <td>Bill To Email</td>
                <td>Number of Cartons</td>
                <td>Line Items</td>
                <td>Alternate ETIN</td>
                <td>Order Message</td>
                <td>Shipped Date</td>
                <td>Ship to</td>
                <td>Ship to State</td>
                <td>Status</td>
                <td>Tracking Number</td>
                <td>Transit Time</td>
                <td>order type</td>
                <td>store</td>
                <td>customer Note</td>
            </tr>  
        </thead>
        <tbody>
            <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($value->etailer_order_number); ?></td>
                    <td><?php echo e($value->channel_order_number); ?></td>
                    <td><?php echo e($value->client_company_name); ?></td>
                    <td><?php echo e($value->warehouse); ?></td>
                    <td><?php echo e(date("m/d/Y H:i:s", strtotime($value->purchase_date))); ?></td>
                    <td><?php echo e($value->company_name); ?>  <?php echo e($value->service_name); ?></td>
                    <td><?php echo e($value->customer_email); ?></td> 
                    <td><?php echo e($value->packageTotal); ?></td> 
                    <td>
                        <?php
                            $array = explode(",", $value->ETIN);
                        ?>
                        <?php $__currentLoopData = $array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $etinval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($etinval); ?><br/>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                    <td>
                        <?php
                            $altarray = explode(",", $value->alternate_ETINs);
                        ?>
                        <?php $__currentLoopData = $altarray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $altetinval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($altetinval); ?> <br/>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                    <td></td>
                    <td><?php echo e(date("m/d/Y", strtotime($value->ship_date))); ?></td>
                    <td><?php echo e($value->ship_to_name); ?>

                        <br><?php echo e($value->ship_to_address1); ?>,
                        <?php echo e($value->ship_to_address2); ?>

                        <?php echo e($value->ship_to_address3); ?><br>
                        <?php echo e($value->ship_to_city); ?>,<?php echo e($value->ship_to_state); ?>,<?php echo e($value->ship_to_zip); ?>

                     </td>
                    <td><?php echo e($value->ship_to_state); ?></td>
                    <td><?php echo e($value->status == 6 ?'Shipped' :'Reship Shipped'); ?></td>
                    <td><?php echo e($value->tracking_number); ?></td>
                    <td><?php echo e($value->transit_days); ?></td>
                    
                    <?php if($value->sub_order_number != null): ?>
                        <?php
                            $temp = '';
                            $tempArray = explode('.',$value->sub_order_number);
                            if(isset($tempArray[1]))
                            {
                                if($tempArray[1] == '001'){$temp= 'Frozen';}
                                elseif($tempArray[1] == '002'){$temp = 'Dry';}
                                elseif($tempArray[1] == '003'){$temp = 'Refrigerated';}
                            }
                        ?>
                    <?php endif; ?>
                    <td><?php echo e($temp); ?></td>
					<td><?php echo e($value->channel_type); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
        </tbody>
    </table>
</body>
</html><?php /**PATH C:\wamp64\www\cranium_new\resources\views/cranium/reports/export/billing_order_report_export.blade.php ENDPATH**/ ?>