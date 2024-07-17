<?php $required = '<span class="text-danger">*</span>'; ?>
<div class="modal-dialog mt-5 modal-lg" style="min-width: 85%">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="createEventModalLabel">Filtered Order</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
            <table id="datatableOrder" class="table table-bordered text-center">
              <thead class="bg-gray-800 text-white">
                    <tr>
                        <th scope="col">Order Date</th>
                        <th scope="col">e-tailer Order Number</th>
                        <th scope="col">Client</th>
                        <th scope="col">Order Source</th>
                        <th scope="col">Destination</th>
                        <th scope="col">Channel Delivery Date</th>
                        <th scope="col">Ship By</th>
                        <th scope="col">Service Type</th>
                        @isset($os)
                          <th scope="col">Picker</th>                          
                        @endisset
                        <th scope="col">Order Status</th>
                        <th>Action</th>
                    </tr>
              </thead>

              <tbody id='tb_order_details'>
                @if(isset($result) && count($result) > 0)
                  @foreach($result as $row)
                      <tr>
                          <td>{{$row->created_at}}</td>
                          <td>{{$row->etailer_order_number}}</td>
                          <td>{{$row->client_name}}</td>
                          <td>{{$row->order_source}}</td>
                          <td>{{$row->ship_to_state}}</td>
                          <td>{{$row->channel_estimated_delivery_date}}</td>
                          <td>{{$row->ship_by_date}}</td>
                          @if (isset($row->carrier_name) && $row->carrier_name !== '')
                            @if (strtolower($row->carrier_name) === 'ups')
                              @foreach ($ups_st as $ups)
                                @if ($row->service_type_id == $ups->id) 
                                  <td>{{$ups->service_name}}</td>
                                @endif
                              @endforeach											
                            @elseif (strtolower($row->carrier_name) === 'fedex')                              
                              @foreach ($fedex_st as $fedex)
                                @if ($row->service_type_id == $fedex->id)
                                  <td>{{$fedex->service_name}}</td>
                                @endif
                              @endforeach
                            @endif
                          @else
                            <td></td>                           
                          @endif
                          @isset($os)
                            <td>{{$row->picker_name}}</td>
                          @endisset
                          <td>{{$row->order_status_name}}</td>
                          <td><a href="{{route('orders.view',$row->id)}}" target="_blank" class="edit btn btn-primary btn-sm">View</a></td>
                      </tr>
                  @endforeach
                @else
                    <tr>
                      <td colspan="9">No Data Present</td>
                    </tr>
                @endif
              </tbody>
            </table>
            <button data-dismiss="modal" type="button" class="btn btn-danger float-right">Close</button>
            @if (isset($o_nums))
              <button class="btn btn-primary submit float-right mr-2" type="button" onClick="downloadTotalOrderCSV({{ isset($os) ? 1 : 0 }}, '{{ implode(',',$o_nums) }}', null)">Download CSV</button>
            @elseif (isset($so_nums))
              <button class="btn btn-primary submit float-right mr-2" type="button" onClick="downloadTotalOrderCSV({{ isset($os) ? 1 : 0 }}, null, '{{ implode(',', $so_nums) }}')">Download CSV</button> 
            @else
              <button class="btn btn-primary submit float-right mr-2" type="button" onClick="downloadTotalOrderCSV({{ isset($os) ? 1 : 0 }}, null)">Download CSV</button>
            @endif
        </div>
      </div>
    </div>
</div>

<script>
  function downloadTotalOrderCSV(isOs, oNums, soNums) {
    if (oNums == null && soNums == null) {
      let elem = document.querySelectorAll('#tb_order_details > tr');
      var data = [
        isOs 
          ? ['Order Date',	'e-tailer Order Number',	'Client',	'Order Source',	'Destination',	'Channel Delivery Date',	'Ship By', 'Picker',	'Order Status']
          : ['Order Date',	'e-tailer Order Number',	'Client',	'Order Source',	'Destination',	'Channel Delivery Date',	'Ship By', 'Order Status']
      ];
      extractDataFromElementForCSV(elem, data, 'orders_details.csv', true);
    } else {

      let form = new FormData();
      if (soNums) form.append('sub_orders', soNums);
      if (oNums) form.append('orders', oNums);        
      form.append('is_os', isOs);   

      $.ajax({
        type: "POST",
        url: '{{ route('pipeline_and_metrix.TotalOrderCSVDownload') }}',
        data: form,
        processData: false,
        contentType: false,
        success: function( response ) {
          downloadBlob(response, 'total_order.csv', 'text/csv;charset=utf-8;');
        },
        error: function(data){
          
        }
      })
    }   
  }
</script>
