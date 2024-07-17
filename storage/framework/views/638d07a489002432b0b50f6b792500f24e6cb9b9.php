     
<div class="modal-dialog modal-lg">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Sub-Order <?php echo e($key); ?> for Reship</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: black;">&times;</button>
        </div> 
        <div class="modal-body tab-pane">
            <form>
                <?php echo csrf_field(); ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="10" class="text-center" style="background:<?php echo e(rand_color()); ?>;">
                                Reship: #<?php echo e($key); ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody>												
                        <tr>
                            <td scope="row" data-placement="top" title="Fault">Fault</td>
                            <td scope="row" data-placement="top">
                                <select class="select2 col-md-6" id="fault_<?php echo e($key); ?>">
                                    <option value="-1">Select</option>
                                    <?php $__currentLoopData = $fault_codes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($fc->id); ?>"><?php echo e($fc->fault); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>										
                                </select>
                            </td>
                        </tr>
                        <tr>
                            ̊<td scope="row" data-placement="top" title="Re-Ship Reason">Re-Ship Reason</td>
                            <td scope="row" data-placement="top">
                                <select class="select2 col-md-6" id="reship_reason_<?php echo e($key); ?>">
                                    <option value="">Select</option>
                                    <?php $__currentLoopData = $reship_codes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($rc->id); ?>"><?php echo e($rc->reason); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>															
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td scope="row" data-placement="top" title="Carrier Type">Carrier Type</td>
                            <td scope="row" data-placement="top">
                                <select class="select2 col-md-6" id="rs_carrier_type" onchange="changeReshipShipmentTypeInSubOrder(this)">
                                    <option value="-1">Select</option>
                                    <?php $__currentLoopData = $carr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($car->company_name); ?>" <?php if($prev_carr_id == $car->id) echo 'selected'; ?> ><?php echo e($car->company_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>										
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td scope="row" data-placement="top" title="Shipment Type">Shipment Type</td>
                            <td scope="row" data-placement="top">
                                <select class="select2 col-md-6" id="rs_shipment_type">
                                    <option value="">Select</option>
                                    <?php if(strtolower($prev_carr_name) === 'ups'): ?>
                                        <?php $__currentLoopData = $ups_st; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ups): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($ups->id); ?>" <?php if ($prev_ship_type == $ups->id) echo 'selected'; ?>><?php echo e($ups->service_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>											
                                    <?php elseif(strtolower($prev_carr_name) === 'fedex'): ?>
                                        <?php $__currentLoopData = $fedex_st; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fedex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($fedex->id); ?>" <?php if ($prev_ship_type == $fedex->id) echo 'selected'; ?>><?php echo e($fedex->service_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <button type="button" class="btn btn-primary float-right" onclick="reShipOrders()">Re-Ship</button>
        </div>
    </div>
</div>

<script>
function reShipOrders() {

    var selectElem = document.getElementById('fault_' + '<?php echo e($key); ?>');
    var faultId = selectElem.options[selectElem.selectedIndex].value;

    selectElem = document.getElementById('reship_reason_' + '<?php echo e($key); ?>');
    var reShipReasonId = selectElem.options[selectElem.selectedIndex].value;

    selectElem = document.getElementById('rs_carrier_type');
    var carrierId = selectElem.options[selectElem.selectedIndex].value;

    selectElem = document.getElementById('rs_shipment_type');
    var shipmentType = selectElem.options[selectElem.selectedIndex].value;

    if (faultId === '' || reShipReasonId === '' || carrierId === '' || shipmentType === '') {
        toastr.error('Fault/Reship-Reason/Shipment/Carrier must be selected.');
        return;
    }

    var form = new FormData();
    form.append('order_number', <?php echo e($on); ?>);
    form.append('sub_order_number', '<?php echo e($key); ?>');
    form.append('ids', '<?php echo e($ids); ?>');
    form.append('fault_id', faultId);
    form.append('reship_reason_id', reShipReasonId);
    form.append('carrier_id', carrierId);
    form.append('shipment_type', shipmentType);

    $.ajax({
        url: '<?php echo e(route('orders.reship_order')); ?>',
        method: 'POST',
        data: form,
        processData: false,
        contentType: false,
        success: function(res) {
            if(res.error === false) {
                toastr.success(res.msg);
                setTimeout(function(){
                    location.reload();
                }, 2000);
            } else {
                toastr.error(res.msg);
            }
        }			
    });
}

function changeReshipShipmentTypeInSubOrder(type) {
    var toAppend = 'Hello'
    if (type.value.toLowerCase() === 'fedex') {
        toAppend = <?php echo json_encode($fedex_st, 15, 512) ?>;
    } else if (type.value.toLowerCase() === 'ups') {
        toAppend = <?php echo json_encode($ups_st, 15, 512) ?>;
    } else {
        toAppend = [];
    }

    var select_elem = document.getElementById('rs_shipment_type');
    var options = select_elem.getElementsByTagName('option');
    for (var i = options.length; i--;) {	
        select_elem.removeChild(options[i]);
    }

    let opt = document.createElement("option");
    opt.value = ''; 
    opt.innerHTML = 'Select'; 
    select_elem.append(opt);

    for (var key in toAppend) {
        let opt = document.createElement("option");
        opt.value = toAppend[key].id; 
        opt.innerHTML = toAppend[key].service_name; 
        select_elem.append(opt); 
    }
}
</script><?php /**PATH C:\wamp64\www\cranium_new\resources\views/orders/view_reship_option.blade.php ENDPATH**/ ?>