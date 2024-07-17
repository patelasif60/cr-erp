@php
  $os_status = array('Open Orders',	'Must Ship Today',	'Available to Ship',	'Hold',	'OOS',	'Picked', 'Packed', 'Processed');
@endphp
<div class="modal-dialog mt-5 modal-lg" style="min-width: 70%">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="createEventModalLabel">Order By Order Status</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table id="datatableOrder" class="table table-bordered text-center min-w-full" style="width:100%">
              <thead class="bg-gray-800 text-white">
                  <tr>
                      <th scope="col">Row Label</th>
                      <th scope="col">Open Orders</th>
                      <th scope="col">Must Ship Today</th>
                      <th scope="col">Available to Ship</th>
                      <th scope="col">On Hold</th>
                      <th scope="col">OOS</th>
                      <th scope="col">Pick</th>
                      <th scope="col">Pack</th>
                      <th scope="col">Shipped</th>
                      <th scope="col">Grand Total</th>
                  </tr>
              </thead>

              <tbody>
                @if(isset($temp_count) && count($temp_count) > 0)
                  @foreach($temp_count as $key => $value)
                    <tr style="font-weight: bold">                          
                        <td align="left">{{ $key }}</td>
                        @foreach ($os_status as $status)
                          @if (isset($value[$status]) && $value[$status] !== '')
                            <td>
                              <a href="javascript:showDetailsOsModal(null, '{{ $value[$status] }}');">
                                {{ count(array_unique(explode(',', $value[$status]))) }}
                              </a>
                            </td>                                     
                          @else                    
                            <td>0</td>                        
                          @endif 
                        @endforeach                        
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
                        @if (count(array_unique($total)) > 0)
                          <td>
                            <a href="javascript:showDetailsOsModal(null, '{{ implode(',', array_unique($total)) }}');">
                              {{ count(array_unique($total)) }}
                            </a>
                          </td>                                     
                        @else                    
                          <td>0</td>                        
                        @endif                    
                    </tr>
                      @if(isset($type_status_count[$key]))
                        @foreach ($type_status_count[$key] as $val_key => $val_value)
                          <tr>
                            <td align="left" class="pl-40">{{ $val_key }}</td>
                            @foreach ($os_status as $status)
                                <td>
                                  @if (isset($val_value[$status][1]))
                                    <a href="javascript:showDetailsOsModal('{{ isset($val_value[$status][1]) ? $val_value[$status][1] : '' }}');">
                                      {{ isset($val_value[$status][0]) && $val_value[$status][0] !== '' ? count(explode(',', $val_value[$status][0])) : 0 }}
                                    </a>
                                  @else
                                    {{ isset($val_value[$status][0]) && $val_value[$status][0] !== '' ? count(explode(',', $val_value[$status][0])) : 0 }}
                                  @endif
                                </td>
                            @endforeach                                      
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
                              @if (count(array_unique($val_total)))
                                <td>
                                  <a href="javascript:showDetailsOsModal(null, '{{ implode(',', array_unique($val_total)) }}');">
                                    {{ count(array_unique($val_total)) }}
                                  </a>
                                </td>                                     
                              @else                    
                                <td>0</td>                        
                              @endif
                          </tr>
                        @endforeach
                      @endif            
                  @endforeach
                @else
                  <tr>
                    <td colspan="10">No Data Present</td>
                  </tr>
                @endif
              </tbody>
          </table>          
        </div>        
        <div id="os_item_table" class="table-responsive" style="display: none">
          <div class="modal-header bg-light">
            <h5 class="modal-title" id="createEventModalLabel">Orders</h5>            
          </div>
          <table id="datatableOrderTd" class="table table-bordered text-center mt-6">
            <thead class="bg-gray-800 text-white">
                  <tr>
                      <th scope="col">Order Date</th>
                      <th scope="col">e-tailer Order Number</th>
                      <th scope="col">Client</th>
                      <th scope="col">Order Source</th>
                      <th scope="col">Destination</th>
                      <th scope="col">Channel Delivery Date</th>
                      <th scope="col">Ship By</th>
                      <th scope="col">Picker</th>
                      <th scope="col">Order Status</th>
                      <th>Action</th>
                  </tr>
            </thead>

            <tbody id="datatableOrderOsBody">              
            </tr>
            </tbody>
          </table>
        </div>
        <button data-dismiss="modal" type="button" class="btn btn-danger float-right">Close</button>            
        <button id="os_show_tb_btn" type="button" class="btn btn-success float-right mr-2" onclick="hideTable()" style="display: none">Hide Details</button>            
      </div>
    </div>
</div>
<script>
  function showDetailsOsModal(subOrderNumbers, orderNumbers) {
    // Remove Old Rows
    removeRows();
    let form1 = new FormData();
    if (subOrderNumbers && subOrderNumbers !== '') form1.append('sub_orders', subOrderNumbers);
    if (orderNumbers && orderNumbers !== '') form1.append('orders', orderNumbers);
    $.ajax({
        type:'POST',
        url: '{{ route('pipeline_and_metrix.GetTransitDayOrderModal') }}',
        data: form1,
        processData: false,
        contentType: false,
        success: function(response){
          if(response.error == 0){         
					  appendRows(response.data);                
				  } else {				
					  toastr.error(response.msg);
				  }
        },
        error: function(data){
          toastr.error('Some Error Occurred.');
        }
    });
  }
  
  function appendRows(data) {
    
    let tableRef = document.getElementById('datatableOrderOsBody');
    let tableRefDiv = document.getElementById('os_item_table');

    for(let i = 0; i < data.length; i++) {      
      let newRow = tableRef.insertRow(-1);
      
      var date = new Date(data[i].created_at);
      let newCell = newRow.insertCell(-1);
      let newText = document.createTextNode(addLeadingZeros(date.getMonth() + 1) + '-' + date.getDate() + '-' + date.getFullYear());
      newCell.appendChild(newText);

      newCell = newRow.insertCell(-1);
      newText = document.createTextNode(data[i].etailer_order_number);
      newCell.appendChild(newText);

      newCell = newRow.insertCell(-1);
      newText = document.createTextNode(data[i].channel_type);
      newCell.appendChild(newText);
      
      newCell = newRow.insertCell(-1);
      newText = document.createTextNode(data[i].order_source);
      newCell.appendChild(newText);
      
      newCell = newRow.insertCell(-1);
      newText = document.createTextNode(data[i].ship_to_state);
      newCell.appendChild(newText);
      
      newCell = newRow.insertCell(-1);
      newText = document
                .createTextNode(data[i].channel_estimated_delivery_date ? data[i].channel_estimated_delivery_date : '');
      newCell.appendChild(newText);
      
      newCell = newRow.insertCell(-1);
      newText = document.createTextNode(data[i].ship_by_date ? data[i].ship_by_date : '');
      newCell.appendChild(newText);

      newCell = newRow.insertCell(-1);
      newText = document.createTextNode(data[i].picker_name ? data[i].picker_name : '');
      newCell.appendChild(newText);
      
      newCell = newRow.insertCell(-1);
      newText = document.createTextNode(data[i].order_status_name);
      newCell.appendChild(newText);
      
      newCell = newRow.insertCell(-1);
      var aTag = document.createElement("a");
      aTag.href = "/summery_orders/" + data[i].id + "/view";
      aTag.target = "_blank";
      aTag.text = 'View'
      aTag.classList.add("edit", "btn", "btn-primary", "btn-sm");
      newCell.appendChild(aTag);
    }      

    tableRefDiv.style.display = "";
    document.getElementById("os_show_tb_btn").style.display = "";
  }

  function hideTable() {
    removeRows();
    document.getElementById("os_item_table").style.display = "none";
    document.getElementById("os_show_tb_btn").style.display = "none";
  }

  function removeRows() {
    let tableRef = document.getElementById('datatableOrderOsBody');
    while(tableRef.rows.length) {
      tableRef.deleteRow(0);
    }
  }

  function addLeadingZeros(n) {
    if (n <= 9) {
      return "0" + n;
    }
    return n
  }
</script>
