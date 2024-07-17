
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {
        var data = google.visualization.arrayToDataTable([
          ['Pack Type', 'Overnight', '2 Day Air', '1D Ground', '2D Ground', '3D Ground', '4D Ground', '5D Ground'],
          <?php echo $chart_data; ?>
        ]);

        var options = {
          vAxis: {
            title: 'Order Count',
            direction: 1
          },
        };

        var chart = new google.charts.Bar(document.getElementById('transit_x_div'));
        function selectHandler() {
          var selectedItem = chart.getSelection()[0];
          if (selectedItem) {
            var topping = data.getValue(selectedItem.row, 0);
            getModal(topping);
          }
        }

        function getModal(packType){
            
            var form_cust = $('#GetTransitDayModal')[0];
            let form1 = new FormData(form_cust);
            form1.append('pack_type', packType);
            $.ajax({
                type:'POST',
                url: '{{ route('pipeline_and_metrix.GetTransitDayModal') }}',
                data: form1,
                processData: false,
                contentType: false,
                success:function(response){                    
                    $("#transitDayModel").html('');
                    $('#transitDayModel').html(response);
                    $('#transitDayModel').modal('show');
                }
            });
        }

        google.visualization.events.addListener(chart, 'select', selectHandler); 
        chart.draw(data, google.charts.Bar.convertOptions(options));
      };

         
    </script>
    <div id="transit_x_div" style="width: 100%; height: 600px;"></div>

    <form id="GetTransitDayModal">
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
    <div class="modal fade" id="transitDayModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
    
