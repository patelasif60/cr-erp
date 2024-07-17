@php
$day = strtolower(jddayofweek(date("w", strtotime('now')) - 1, 1));
if ($day === 'monday') {
  $start_date = date('Y-m-d', strtotime('-7 days', time()));
  $end_date = date('Y-m-d', (strtotime('previous Friday')));
} else {  
  $start_date = date('Y-m-d', strtotime('previous Monday'));
  $end_date = date('Y-m-d', strtotime('this Friday'));
}
$users = [];
if (Auth::user()->role == 1) {
  $users = \App\User::all();
} else {
  $users = \App\User::where('id', Auth::user()->id)->get();
}
@endphp
<form action="" method="post" id="GetOrderRecordsByUser">
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
        <label for="user">Users</label>
          <select class="form-control select2" name="user[]" id="user" multiple size="3">
            <option value="">Select</option>
            @isset($users)
              @foreach($users as $user)
                <option value="{{$user->id}}">{{$user->name}}</option>
              @endforeach
            @endisset
          </select>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <button class="btn btn-primary submit" type="button" onClick="CallAChartByUsers()">Search</button>
    </div>
  </div>
</form>

<div id="CallAChartByUsers" class="mt-5"></div>

<script type="text/javascript">
  $(function(){
    CallAChartByUsers();
  })

  // $('#start_date_orders').flatpickr({
  //     static: true,
  //     enableTime: false,
  //     dateFormat: "d-m-Y",
  // });
  function CallAChartByUsers(){
    var form_cust = $('#GetOrderRecordsByUser')[0];
    let form1 = new FormData(form_cust);
    $.ajax({
      type: "POST",
      url: '{{ route('pipeline_and_metrix.GetTotalOrderChartByUser') }}',
      data: form1,
      processData: false,
      contentType: false,
      success: function( response ) {
        $("#CallAChartByUsers").html(response);
        $(".submit").attr("disabled", false);
      },
      error: function(data){
        $(".submit").attr("disabled", false);
      }
    })
  }


</script>