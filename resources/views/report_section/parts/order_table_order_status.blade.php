@php
  $os_status = array('Open Orders',	'Must Ship Today',	'Available to Ship',	'Hold',	'OOS',	'Picked', 'Packed', 'Processed');
@endphp
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
    @if(isset($client_count_array) && count($client_count_array) > 0)
      @foreach ($client_count_array as $p_key => $values)        
        <tr style="font-weight: bold">
          <td style="text-align: left">{{ str_replace('_', ' ', $p_key) }}</td>
          @foreach ($os_status as $status)
            @if (isset($client_count_array[$p_key][$status]))
              <td>
                <a href="javascript:showDetailsOs(null, '{{ $client_count_array[$p_key][$status] }}');">
                  {{ count(array_unique(explode(',', $client_count_array[$p_key][$status]))) }}
                </a>
              </td>                  
            @else                    
              <td>0</td>                        
            @endif 
          @endforeach
          @if ($count[$p_key] !== '')
            <td>
              <a href="javascript:showDetailsOs(null, '{{ implode(',', array_unique(explode(',', $count[$p_key]))) }}');">
                {{ count(array_unique(explode(',', $count[$p_key]))) }}
              </a>
            </td>
          @else
            <td>0</td>
          @endif               
        </tr>
        @if (isset($temp_count[str_replace('_', ' ', $p_key)]))
          @foreach($temp_count[str_replace('_', ' ', $p_key)] as $key => $value)
            <tr style="font-weight: bold">                          
                <td style="text-align: left" class="pl-40">{{ $key }}</td>        
                @foreach ($os_status as $status)
                  @if (isset($value[$status]) && $value[$status] !== '')
                    <td>
                      <a href="javascript:showDetailsOs(null, '{{ $value[$status] }}');">
                        {{ count(explode(',', $value[$status])) }}
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
                    <a href="javascript:showDetailsOs(null, '{{ implode(',', array_unique($total)) }}');">
                      {{ count(array_unique($total)) }}
                    </a>
                  </td>                  
                @else                    
                  <td>0</td>                        
                @endif
            </tr>
            @if(isset($type_status_count[str_replace('_', ' ', $p_key)][$key]))              
              @foreach ($type_status_count[str_replace('_', ' ', $p_key)][$key] as $val_key => $val_value)
                <tr>
                  <td style="text-align: left" class="pl-80">{{ $val_key }}</td>
                  @foreach ($os_status as $status)
                    @if (isset($value[$status]) && $value[$status] !== '')
                      <td>
                        @if (isset($val_value[$status][1]))
                          <a href="javascript:showDetailsOs('{{ isset($val_value[$status][1]) ? $val_value[$status][1] : '' }}');">
                            {{ isset($val_value[$status][0]) && $val_value[$status][0] !== '' ? count(explode(',', $val_value[$status][0])) : 0 }}
                          </a>
                        @else
                          {{ isset($val_value[$status][0]) && $val_value[$status][0] !== '' ? count(explode(',', $val_value[$status][0])) : 0 }}
                        @endif
                      </td>                  
                    @else                    
                      <td>0</td>                        
                    @endif 
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
                  @if (count(array_unique($total)) > 0)
                    <td>
                      <a href="javascript:showDetailsOs('', '{{ implode(',', array_unique($val_total)) }}');">
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
        @endif
      @endforeach      
    @else
      <tr>
        <td colspan="10">No Data Present</td>
      </tr>
    @endif
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
        url: '{{ route('pipeline_and_metrix.GetOrderModal') }}',
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
<div class="modal fade" id="os_order_detail" tabindex="-1" role="dialog" aria-labelledby="os_order_detail" aria-hidden="true"></div>