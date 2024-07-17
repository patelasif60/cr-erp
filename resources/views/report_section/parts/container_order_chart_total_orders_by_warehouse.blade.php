@php
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
<form action="" method="post" id="GetOrderRecordsByWarehouse">
  <div class="row">
    <div class="col-3">
      <div class="form-group">
        <label for="start_date">Start Date</label>
        <input type="date" name="start_date" id="start_date" class="form-control w-100" value="{{ $start_date }}">
      </div>
    </div>
    <div class="col-3">
      <div class="form-group">
        <label for="end_date">End Date</label>
        <input type="date" name="end_date" id="end_date" class="form-control date w-100" value="{{ $end_date }}">
      </div>
    </div>
    <div class="col-3">
      <div class="form-group">
        <label for="warehouse">Warehouse</label>
          <select class="form-control select2" name="wh_warehouse" id="wh_warehouse" <?php if (isset(Auth::user()->wh_id) && Auth::user()->role != 1) {echo 'disabled';} ?>>
            <option value="">Select</option>
            @if(isset($warehouses))
              @foreach($warehouses as $row_warehouse)
                <option value="{{$row_warehouse->id}}" <?php if (isset(Auth::user()->wh_id) && $row_warehouse->id == Auth::user()->wh_id) {echo 'selected';} ?>>{{$row_warehouse->warehouses}}</option>
              @endforeach
            @endif
          </select>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <button class="btn btn-primary submit" type="button" onClick="CallAChartByWarehouse()">Search</button>
    </div>
  </div>
</form>

<div id="CallAChartByWarehouse" class="mt-5"></div>

<script type="text/javascript">
  $(function(){
    CallAChartByWarehouse();
  })

  // $('#start_date_orders').flatpickr({
  //     static: true,
  //     enableTime: false,
  //     dateFormat: "d-m-Y",
  // });
  function CallAChartByWarehouse(){
    var form_cust = $('#GetOrderRecordsByWarehouse')[0];
    let form1 = new FormData(form_cust);
    $.ajax({
      type: "POST",
      url: '{{ route('pipeline_and_metrix.GetTotalOrderChartByWarehouse') }}',
      data: form1,
      processData: false,
      contentType: false,
      success: function( response ) {
        $("#CallAChartByWarehouse").html(response);
        $(".submit").attr("disabled", false);
      },
      error: function(data){
        $(".submit").attr("disabled", false);
      }
    })
  }


</script>