@php
$clients = \App\Client::select('id','company_name')->get();
$order_statues = \App\OrderSummaryStatus::all();
$warehouses = \App\WareHouse::all();
$day = strtolower(jddayofweek(date("w", strtotime('now')) - 1, 1));
if ($day === 'monday') {
  $start_date = date('Y-m-d', strtotime('-7 days', time()));
  $end_date = date('Y-m-d', (strtotime('previous Friday')));
} else {  
  $start_date = date('Y-m-d', strtotime('previous Monday'));
  $end_date = date('Y-m-d', strtotime('this Friday'));
}
@endphp
<form action="" method="post" id="GetOrderRecords">
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
        <input type="date" name="end_date" id="end_date" class="form-control w-100" value="{{ $end_date }}">
      </div>
    </div>
    <div class="col-3">
      <div class="form-group">
        <label for="order_status">Status</label>
          <select class="form-control select2" name="order_status[]" id="order_status" multiple size="3">
            <option value="">Select</option>
            @if($order_statues)
              @foreach($order_statues as $row_status)
                <option value="{{$row_status->id}}">{{$row_status->order_status_name}}</option>
              @endforeach
            @endif
          </select>
      </div>
    </div>
    <div class="col-3">
      <div class="form-group">
        <label for="client_id">Client</label>
          <select class="form-control select2" name="client_id[]" id="client_id" multiple size="3">
            <option value="">Select</option>
            @if($clients)
              @foreach($clients as $row_client)
                <option value="{{$row_client->id}}">{{$row_client->company_name}}</option>
              @endforeach
            @endif
          </select>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-3">
      <div class="form-group">
        <label for="warehouse">Warehouse</label>
          <select class="form-control select2" name="to_warehouse" id="to_warehouse" <?php if (isset(Auth::user()->wh_id) && Auth::user()->role != 1) {echo 'disabled';} ?>>
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
      <button class="btn btn-primary submit" type="button" onClick="CallAChart()">Search</button>
    </div>
  </div>
</form>

<div id="CallAChart" class="mt-5"></div>

<script type="text/javascript">
  $(function(){
    CallAChart();
  })

  // $('#start_date_orders').flatpickr({
  //     static: true,
  //     enableTime: false,
  //     dateFormat: "d-m-Y",
  // });
  function CallAChart(){
    var form_cust = $('#GetOrderRecords')[0];
    let form1 = new FormData(form_cust);
    $.ajax({
      type: "POST",
      url: '{{ route('pipeline_and_metrix.GetTotalOrderChart') }}',
      data: form1,
      processData: false,
      contentType: false,
      success: function( response ) {
        $("#CallAChart").html(response);
        $(".submit").attr("disabled", false);
      },
      error: function(data){
        $(".submit").attr("disabled", false);
      }
    })
  }


</script>