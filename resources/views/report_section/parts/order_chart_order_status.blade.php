
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {
        var data = google.visualization.arrayToDataTable([
          ['Pack Type', 'OPEN ORDERS', 'MUST SHIP TODAY', 'AVAILABLE TO SHIP', 'ON HOLD', 'OOS', 'SHIPPED', 'PICK', 'PACK'],
          <?php echo $chart_data; ?>
        ]);

        var options = {
          vAxis: {
            title: 'Order Count',
            direction: 1
          },
        };

        var chart = new google.charts.Bar(document.getElementById('order_status_x_div'));
        function selectHandler() {
          var selectedItem = chart.getSelection()[0];
          if (selectedItem) {
            var topping = data.getValue(selectedItem.row, 0);
            getOrderStatusModal(topping);
          }
        }

        function getOrderStatusModal(packType){
            
            var form_cust = $('#GetOrderStatusModal')[0];
            let form1 = new FormData(form_cust);
            form1.append('pack_type', packType);
            $.ajax({
                type:'POST',
                url: '{{ route('pipeline_and_metrix.GetClientOrdersByOrderStatusModal') }}',
                data: form1,
                processData: false,
                contentType: false,
                success:function(response){
                    $("#orderStatusModel").html('');
                    $('#orderStatusModel').html(response);
                    $('#orderStatusModel').modal('show');
                }
            });
        }

        google.visualization.events.addListener(chart, 'select', selectHandler); 
        chart.draw(data, google.charts.Bar.convertOptions(options));
      };

         
    </script>
    <div id="order_status_x_div" style="width: 100%; height: 600px;"></div>

    <form id="GetOrderStatusModal">
        @if(isset($input))
            @foreach($input as $key_input => $row_input)
                <input type="hidden" name="{{$key_input}}" value="{{json_encode($row_input)}}">
            @endforeach
        @endif
        @if(isset($pack_type_order_number))
            @foreach($pack_type_order_number as $key_input => $row_input)
                <input type="hidden" name="{{$key_input}}" value="{{$row_input}}">
            @endforeach
        @endif
    </form>
    <div class="modal fade" id="orderStatusModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
    
