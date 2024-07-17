<?php
$clients = \App\Client::select('id','company_name')->get();
$day = strtolower(jddayofweek(date("w", strtotime('now')) - 1, 1));
if ($day === 'monday') {
  $start_date = date('Y-m-d', strtotime('-7 days', time()));
  $end_date = date('Y-m-d', (strtotime('previous Friday')));
} else {  
  $start_date = date('Y-m-d', strtotime('previous Monday'));
  $end_date = date('Y-m-d', strtotime('this Friday'));
}
$warehouses = \App\WareHouse::all();
?>
<form action="" method="post" id="transit_tb_form_input">
  <div class="row">
    <div class="col-3">
      <div class="form-group">
        <label for="tb_start_date">Start Date</label>
        <input type="date" name="tb_start_date" id="tb_start_date" class="form-control w-100" value="<?php echo e($start_date); ?>">      
      </div>
    </div>
    <div class="col-3">
      <div class="form-group">
        <label for="tb_end_date">End Date</label>
        <input type="date" name="tb_end_date" id="tb_end_date" class="form-control date w-100" value="<?php echo e($end_date); ?>">
      </div>
    </div>
    <div class="col-3">
      <div class="form-group">
        <label for="tb_client_id">Client</label>
          <select class="form-control select2" name="tb_client_id_transit[]" id="tb_client_id_transit" size="3" multiple>
            <option value="">Select</option>
            <?php if($clients): ?>
              <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($row_client->id); ?>"><?php echo e($row_client->company_name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
          </select>
      </div>
    </div>
    <div class="col-3">
      <div class="form-group">
        <label for="td_warehouse">Warehouse</label>
          <select multiple class="form-control select2" name="td_warehouse[]" id="td_warehouse" <?php if (isset(Auth::user()->wh_id) && Auth::user()->role != 1) {echo 'disabled';} ?>>
            <option value="">Select</option>
            <?php if(isset($warehouses)): ?>
              <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row_warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($row_warehouse->warehouses); ?>" <?php if (isset(Auth::user()->wh_id) && $row_warehouse->id == Auth::user()->wh_id) {echo 'selected';} ?>><?php echo e($row_warehouse->warehouses); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
          </select>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <button class="btn btn-primary submit" type="button" onClick="tableByTransitDays(null)">Search</button>
      <button class="btn btn-primary submit" type="button" onClick="tableByTransitDays(1)">Current Week</button>
      <button class="btn btn-primary submit" type="button" onClick="tableByTransitDays(-1)">Previous Week</button>
      <button class="btn btn-primary submit" type="button" onClick="downloadTransitDayCSV()">Download CSV</button>
    </div>
  </div>
  <div class="row"></div>
</form>

<div class="mt-5" id="tableByTransitDays"></div>

<script>
  $(function(){
    tableByTransitDays(null);
  })

  function tableByTransitDays(type){
    var form_cust = $('#transit_tb_form_input')[0];
    let form1 = new FormData(form_cust);

    if (type != null) {
      let wDate = new Date();
      let dDay = wDate.getDay() > 0 ? wDate.getDay() : 7;
      let first = type == -1 ? ((wDate.getDate() - dDay + 1) - 7) : wDate.getDate() - dDay + 1;
      
      let firstDayWeek = new Date(wDate.setDate(first));
      let lastDayWeek = new Date(wDate.setDate(firstDayWeek.getDate() + 4));

      let firstDateStr = firstDayWeek.toISOString().substring(0, 10);
      let lastDateStr = lastDayWeek.toISOString().substring(0, 10);

      document.getElementById('tb_start_date').value = firstDateStr;
      document.getElementById('tb_end_date').value = lastDateStr;

      form1.set('tb_start_date', firstDateStr);
      form1.set('tb_end_date', lastDateStr);
    }

    $.ajax({
      type: "POST",
      url: '<?php echo e(route('pipeline_and_metrix.GetTransitDayTable')); ?>',
      data: form1,
      processData: false,
      contentType: false,
      success: function( response ) {
        $("#tableByTransitDays").html(response);
        $(".submit").attr("disabled", false);
      },
      error: function(data){
        $(".submit").attr("disabled", false);
      }
    })
  }
</script>
<?php /**PATH C:\wamp64\www\cranium_new\resources\views/report_section/parts/container_tb_total_td.blade.php ENDPATH**/ ?>