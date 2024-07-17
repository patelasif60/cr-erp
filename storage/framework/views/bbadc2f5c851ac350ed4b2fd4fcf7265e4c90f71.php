<?php
  $os_status = array('Open Orders',	'Must Ship Today',	'Available to Ship',	'Hold',	'OOS',	'Picked', 'Packed', 'Processed');
?>
<table id="datatableOrderStatusTb" class="table table-bordered text-center min-w-full" style="width:100%">
  <thead class="bg-gray-800 text-white">
      <tr>
        <th scope="col">Row Label</th>
        <th scope="col">Open Orders</th>
        <th scope="col">Must Ship Today</th>
        <th scope="col">Available to Ship</th>
        <th scope="col">On Hold</th>
        <th scope="col">OOS</th>
        <th scope="col">Picked</th>
        <th scope="col">Packed</th>
        <th scope="col">Shipped</th>
        <th scope="col">Grand Total</th>
      </tr>
  </thead>

  <tbody id="tb_os">
    <?php if(isset($client_count_array) && count($client_count_array) > 0): ?>
      <?php $__currentLoopData = $client_count_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p_key => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>        
        <tr style="font-weight: bold">
          <td style="text-align: left"><?php echo e(str_replace('_', ' ', $p_key)); ?></td>
          <?php $__currentLoopData = $os_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($client_count_array[$p_key][$status])): ?>
              <td>
                <a href="javascript:showDetailsOs(null, '<?php echo e($client_count_array[$p_key][$status]); ?>');">
                  <?php echo e(count(array_unique(explode(',', $client_count_array[$p_key][$status])))); ?>

                </a>
              </td>                  
            <?php else: ?>                    
              <td>0</td>                        
            <?php endif; ?> 
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php if($count[$p_key] !== ''): ?>
            <td>
              <a href="javascript:showDetailsOs(null, '<?php echo e(implode(',', array_unique(explode(',', $count[$p_key])))); ?>');">
                <?php echo e(count(array_unique(explode(',', $count[$p_key])))); ?>

              </a>
            </td>
          <?php else: ?>
            <td>0</td>
          <?php endif; ?>               
        </tr>
        <?php if(isset($temp_count[str_replace('_', ' ', $p_key)])): ?>
          <?php $__currentLoopData = $temp_count[str_replace('_', ' ', $p_key)]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr style="font-weight: bold">                          
                <td style="text-align: left" class="pl-40"><?php echo e($key); ?></td>        
                <?php $__currentLoopData = $os_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php if(isset($value[$status]) && $value[$status] !== ''): ?>
                    <td>
                      <a href="javascript:showDetailsOs(null, '<?php echo e($value[$status]); ?>');">
                        <?php echo e(count(explode(',', $value[$status]))); ?>

                      </a>
                    </td>                  
                  <?php else: ?>                    
                    <td>0</td>                        
                  <?php endif; ?> 
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                
                <?php
                  $total = array();
                  foreach ($os_status as $status) {
                    if (isset($value[$status]) && $value[$status] !== '') { 
                      $elems = explode(',', $value[$status]); 
                      foreach($elems as $elem) { 
                        array_push($total, $elem); 
                      } 
                    }      
                  }                  
                ?>
                <?php if(count(array_unique($total)) > 0): ?>
                  <td>
                    <a href="javascript:showDetailsOs(null, '<?php echo e(implode(',', array_unique($total))); ?>');">
                      <?php echo e(count(array_unique($total))); ?>

                    </a>
                  </td>                  
                <?php else: ?>                    
                  <td>0</td>                        
                <?php endif; ?>
            </tr>
            <?php if(isset($type_status_count[str_replace('_', ' ', $p_key)][$key])): ?>              
              <?php $__currentLoopData = $type_status_count[str_replace('_', ' ', $p_key)][$key]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val_key => $val_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td style="text-align: left" class="pl-80"><?php echo e($val_key); ?></td>
                  <?php $__currentLoopData = $os_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(isset($value[$status]) && $value[$status] !== ''): ?>
                      <td>
                        <?php if(isset($val_value[$status][1])): ?>
                          <a href="javascript:showDetailsOs('<?php echo e(isset($val_value[$status][1]) ? $val_value[$status][1] : ''); ?>');">
                            <?php echo e(isset($val_value[$status][0]) && $val_value[$status][0] !== '' ? count(explode(',', $val_value[$status][0])) : 0); ?>

                          </a>
                        <?php else: ?>
                          <?php echo e(isset($val_value[$status][0]) && $val_value[$status][0] !== '' ? count(explode(',', $val_value[$status][0])) : 0); ?>

                        <?php endif; ?>
                      </td>                  
                    <?php else: ?>                    
                      <td>0</td>                        
                    <?php endif; ?> 
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>          
                  <?php
                    $val_total = array();
                    foreach ($os_status as $status) {
                      if (isset($val_value[$status][0]) && $val_value[$status][0] !== '') { 
                        $elems = explode(',', $val_value[$status][0]); 
                        foreach($elems as $elem) { 
                          array_push($val_total, $elem); 
                        } 
                      }  
                    }                    
                  ?>
                  <?php if(count(array_unique($total)) > 0): ?>
                    <td>
                      <a href="javascript:showDetailsOs('', '<?php echo e(implode(',', array_unique($val_total))); ?>');">
                        <?php echo e(count(array_unique($val_total))); ?>

                      </a>
                    </td>                  
                  <?php else: ?>                    
                    <td>0</td>                        
                  <?php endif; ?>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>           
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>      
    <?php else: ?>
      <tr>
        <td colspan="10">No Data Present</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
<script>
  function downloadOrderStatusCSV() {
    
    let elem = document.querySelectorAll('#tb_os > tr');
    var data = [
      ['Row Labal', 'Open Orders',	'Must Ship Today',	'Available to Ship',	'Hold',	'OOS',	'Processed',	'Picked', 'Packed', 'Grand Total']
    ];
    extractDataFromElementForCSV(elem, data, 'orders_by_order_status.csv');    
  }  

  function showDetailsOs(subOrderNumbers, orderNumbers) {
    let form1 = new FormData();
    if (subOrderNumbers && subOrderNumbers !== '') form1.append('sub_orders', subOrderNumbers);
    if (orderNumbers && orderNumbers !== '') form1.append('orders', orderNumbers);
    form1.append('os', 1);
    $.ajax({
        type:'POST',
        url: '<?php echo e(route('pipeline_and_metrix.GetOrderModal')); ?>',
        data: form1,
        processData: false,
        contentType: false,
        success:function(response){
            $("#os_order_detail").html('');
            $('#os_order_detail').html(response);
            $('#os_order_detail').modal('show');
        }
    });
  }
</script>
<div class="modal fade" id="os_order_detail" tabindex="-1" role="dialog" aria-labelledby="os_order_detail" aria-hidden="true"></div><?php /**PATH C:\wamp64\www\cranium_new\resources\views/report_section/parts/order_table_order_status.blade.php ENDPATH**/ ?>