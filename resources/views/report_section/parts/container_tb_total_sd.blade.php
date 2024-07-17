@php
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
@endphp
<form action="" method="post" id="sd_tb_form_input">
  <div class="row">
    <div class="col-3">
      <div class="form-group">
        <label for="sd_tb_start_date">Start Date</label>
        <input type="date" name="sd_tb_start_date" id="sd_tb_start_date" class="form-control w-100" value="{{ $start_date }}">      
      </div>
    </div>
    <div class="col-3">
      <div class="form-group">
        <label for="sd_tb_end_date">End Date</label>
        <input type="date" name="sd_tb_end_date" id="sd_tb_end_date" class="form-control date w-100" value="{{ $end_date }}">
      </div>
    </div>
    <div class="col-3">
      <div class="form-group">
        <label for="sd_tb_client_id">Client</label>
          <select class="form-control select2" name="sd_tb_client_id[]" id="sd_tb_client_id" size="3" multiple>
            <option value="">Select</option>
            @if($clients)
              @foreach($clients as $row_client)
                <option value="{{$row_client->id}}">{{$row_client->company_name}}</option>
              @endforeach
            @endif
          </select>
      </div>
    </div>
    <div class="col-3">
      <div class="form-group">
        <label for="sd_warehouse">Warehouse</label>
          <select multiple class="form-control select2" name="sd_warehouse[]" id="sd_warehouse" <?php if (isset(Auth::user()->wh_id) && Auth::user()->role != 1) {echo 'disabled';} ?>>
            <option value="">Select</option>
            @if(isset($warehouses))
              @foreach($warehouses as $row_warehouse)
                <option value="{{$row_warehouse->warehouses}}" <?php if (isset(Auth::user()->wh_id) && $row_warehouse->id == Auth::user()->wh_id) {echo 'selected';} ?>>{{$row_warehouse->warehouses}}</option>
              @endforeach
            @endif
          </select>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <button class="btn btn-primary submit" type="button" onClick="tableByShipDay()">Search</button>
      <button class="btn btn-primary submit" type="button" onClick="tableByShipDay(1)">Current Week</button>
      <button class="btn btn-primary submit" type="button" onClick="tableByShipDay(-1)">Previous Week</button>
      <button class="btn btn-primary submit" type="button" onClick="downloadShipDayCSV()">Download CSV</button>
    </div>
  </div>
  <div class="row"></div>
</form>

<div class="mt-5" id="tableByShipDay"></div>

<script>
  $(function(){
    tableByShipDay(null);
  })

  function tableByShipDay(type){
    var form_cust = $('#sd_tb_form_input')[0];
    let form1 = new FormData(form_cust);

    if (type != null) {
      let wDate = new Date();
      let dDay = wDate.getDay() > 0 ? wDate.getDay() : 7;
      let first = type == -1 ? ((wDate.getDate() - dDay + 1) - 7) : wDate.getDate() - dDay + 1;
      
      let firstDayWeek = new Date(wDate.setDate(first));
      let lastDayWeek = new Date(wDate.setDate(firstDayWeek.getDate() + 4));

      let firstDateStr = firstDayWeek.toISOString().substring(0, 10);
      let lastDateStr = lastDayWeek.toISOString().substring(0, 10);

      document.getElementById('sd_tb_start_date').value = firstDateStr;
      document.getElementById('sd_tb_end_date').value = lastDateStr;

      form1.set('sd_tb_start_date', firstDateStr);
      form1.set('sd_tb_end_date', lastDateStr);
    }

    $.ajax({
      type: "POST",
      url: '{{ route('pipeline_and_metrix.GetClientOrdersTableByShipDay') }}',
      data: form1,
      processData: false,
      contentType: false,
      success: function( response ) {
        $("#tableByShipDay").html(response);
        $(".submit").attr("disabled", false);
      },
      error: function(data){
        $(".submit").attr("disabled", false);
      }
    })
  }
</script>
