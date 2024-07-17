<table id="datatableOrderTb" class="table table-bordered text-center min-w-full" style="width:100%">
  <thead class="bg-gray-800 text-white">
      <tr>
          <th scope="col" style="text-align: left">Row Label</th>
          <th scope="col">Overnight</th>
          <th scope="col">2 Day Air</th>
          <th scope="col">1D Ground</th>
          <th scope="col">2D Ground</th>
          <th scope="col">3D Ground</th>
          <th scope="col">4D Ground</th>
          <th scope="col">5D Ground</th>
          <th scope="col">Grand Total</th>
      </tr>
  </thead>

  <tbody id="tb_td">
    <?php if(isset($client_count_array) && count($client_count_array) > 0): ?>
      <?php $__currentLoopData = $client_count_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p_key => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>        
        <tr style="font-weight: bold">
          <td style="text-align: left"><?php echo e(str_replace('_', ' ', $p_key)); ?></td>
          <?php $__currentLoopData = range(-1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>  
            <?php if(isset($client_count_array[$p_key][$day])): ?>
              <td>
                <a href="javascript:showDetailsTd('<?php echo e($client_count_array[$p_key][$day]); ?>');">
                  <?php echo e(count(array_unique(explode(',', $client_count_array[$p_key][$day])))); ?>

                </a>
              </td>                  
            <?php else: ?>                    
              <td>0</td>                        
            <?php endif; ?>     
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php if($count[$p_key] !== ''): ?>
            <td>
              <a href="javascript:showDetailsTd('<?php echo e(implode(',', array_unique(explode(',', $count[$p_key])))); ?>');">
                <?php echo e(count(array_unique(explode(',', $count[$p_key])))); ?>

              </a>
            </td>
          <?php else: ?>
            <td>0</td>
          <?php endif; ?>
        </tr>
        <?php if(isset($temp_count[$p_key])): ?>      
          <?php $__currentLoopData = $temp_count[$p_key]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr style="font-weight: bold">                          
                <td style="text-align: left" class="pl-40"><?php echo e($key); ?></td>
                <?php $__currentLoopData = range(-1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>  
                  <?php if(isset($value[$day]) && $value[$day] !== ''): ?>
                    <td>
                      <a href="javascript:showDetailsTd('<?php echo e($value[$day]); ?>');">
                        <?php echo e(count(array_unique(explode(',', $value[$day])))); ?>

                      </a>
                    </td>                  
                  <?php else: ?>                    
                    <td>0</td>                        
                  <?php endif; ?>      
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php
                  $total = array();
                  foreach (range(-1, 5) as $day) {
                    if (isset($value[$day]) && $value[$day] !== '') { 
                      $elems = explode(',', $value[$day]); 
                      foreach($elems as $elem) { 
                        array_push($total, $elem); 
                      } 
                    } 
                  }                  
                ?>
                <?php if(count(array_unique($total)) > 0): ?>
                  <td>
                    <a href="javascript:showDetailsTd('<?php echo e(implode(',', array_unique($total))); ?>');">
                      <?php echo e(count(array_unique($total))); ?>

                    </a>
                  </td>                  
                <?php else: ?>                    
                  <td>0</td>                        
                <?php endif; ?>
            </tr>
            <?php if(isset($type_transit_count[$p_key][$key])): ?>
              <?php $__currentLoopData = $type_transit_count[$p_key][$key]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val_key => $val_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="text-align: left" class="pl-80"><?php echo e($val_key); ?></td>
                    <?php $__currentLoopData = range(-1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if(isset($val_value[$day][1]) && $val_value[$day][1] !== ''): ?>
                        <td>
                          <a href="javascript:showDetailsTd('<?php echo e($val_value[$day][1]); ?>');">
                            <?php echo e($val_value[$day][0]); ?>

                          </a>
                        </td>
                      <?php else: ?>
                        <td>0</td>
                      <?php endif; ?>                    
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php
                      $val_total = array();
                      foreach (range(-1, 5) as $day) {
                        if (isset($val_value[$day][1]) && $val_value[$day][1] !== '') { 
                          $elems = explode(',', $val_value[$day][1]); 
                          foreach($elems as $elem) { 
                            array_push($val_total, $elem); 
                          } 
                        }  
                      } 
                    ?>
                    <?php if(count(array_unique($val_total)) > 0): ?>
                      <td>
                        <a href="javascript:showDetailsTd('<?php echo e(implode(',', array_unique($val_total))); ?>');">
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
        <td colspan="9">No Data Present</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
<script>

  function downloadTransitDayCSV() {
    
    let elem = document.querySelectorAll('#tb_td > tr');    
    var data = [
      ['Row Label', 'Overnight', '2 Day Air', '1D Ground',	'2D Ground',	'3D Ground',	'4D Ground',	'5D Ground',	'Grand Total']
    ];
    extractDataFromElementForCSV(elem, data, 'orders_by_transit_day.csv');
  }

  function showDetailsTd(subOrderNumbers) {
    let form1 = new FormData();
    form1.append('sub_orders', subOrderNumbers);
    $.ajax({
        type:'POST',
        url: '<?php echo e(route('pipeline_and_metrix.GetOrderModal')); ?>',
        data: form1,
        processData: false,
        contentType: false,
        success:function(response){
            $("#td_order_detail").html('');
            $('#td_order_detail').html(response);
            $('#td_order_detail').modal('show');
        }
    });
  }
</script>
<div class="modal fade" id="td_order_detail" tabindex="-1" role="dialog" aria-labelledby="td_order_detail" aria-hidden="true"></div><?php /**PATH C:\wamp64\www\cranium_new\resources\views/report_section/parts/order_table_transit.blade.php ENDPATH**/ ?>