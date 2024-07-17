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
    @if(isset($client_count_array) && count($client_count_array) > 0)
      @foreach ($client_count_array as $p_key => $values)        
        <tr style="font-weight: bold">
          <td style="text-align: left">{{ str_replace('_', ' ', $p_key) }}</td>
          @foreach (range(-1, 5) as $day)  
            @if (isset($client_count_array[$p_key][$day]))
              <td>
                <a href="javascript:showDetailsTd('{{ $client_count_array[$p_key][$day] }}');">
                  {{ count(array_unique(explode(',', $client_count_array[$p_key][$day]))) }}
                </a>
              </td>                  
            @else                    
              <td>0</td>                        
            @endif     
          @endforeach
          @if ($count[$p_key] !== '')
            <td>
              <a href="javascript:showDetailsTd('{{ implode(',', array_unique(explode(',', $count[$p_key]))) }}');">
                {{ count(array_unique(explode(',', $count[$p_key]))) }}
              </a>
            </td>
          @else
            <td>0</td>
          @endif
        </tr>
        @if (isset($temp_count[$p_key]))      
          @foreach($temp_count[$p_key] as $key => $value)
            <tr style="font-weight: bold">                          
                <td style="text-align: left" class="pl-40">{{ $key }}</td>
                @foreach (range(-1, 5) as $day)  
                  @if (isset($value[$day]) && $value[$day] !== '')
                    <td>
                      <a href="javascript:showDetailsTd('{{ $value[$day] }}');">
                        {{ count(array_unique(explode(',', $value[$day]))) }}
                      </a>
                    </td>                  
                  @else                    
                    <td>0</td>                        
                  @endif      
                @endforeach
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
                @if (count(array_unique($total)) > 0)
                  <td>
                    <a href="javascript:showDetailsTd('{{ implode(',', array_unique($total)) }}');">
                      {{ count(array_unique($total)) }}
                    </a>
                  </td>                  
                @else                    
                  <td>0</td>                        
                @endif
            </tr>
            @if(isset($type_transit_count[$p_key][$key]))
              @foreach ($type_transit_count[$p_key][$key] as $val_key => $val_value)
                <tr>
                    <td style="text-align: left" class="pl-80">{{ $val_key }}</td>
                    @foreach (range(-1, 5) as $day)
                      @if (isset($val_value[$day][1]) && $val_value[$day][1] !== '')
                        <td>
                          <a href="javascript:showDetailsTd('{{ $val_value[$day][1] }}');">
                            {{ $val_value[$day][0] }}
                          </a>
                        </td>
                      @else
                        <td>0</td>
                      @endif                    
                    @endforeach
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
                    @if (count(array_unique($val_total)) > 0)
                      <td>
                        <a href="javascript:showDetailsTd('{{ implode(',', array_unique($val_total)) }}');">
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
        <td colspan="9">No Data Present</td>
      </tr>
    @endif
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
        url: '{{ route('pipeline_and_metrix.GetOrderModal') }}',
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
<div class="modal fade" id="td_order_detail" tabindex="-1" role="dialog" aria-labelledby="td_order_detail" aria-hidden="true"></div>