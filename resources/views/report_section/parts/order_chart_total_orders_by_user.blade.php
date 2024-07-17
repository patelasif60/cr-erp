
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Picked', 'Packed', 'Shipped'],
          <?php if (isset($chart_data)) echo $chart_data;?>
        ]);

        var options = {
          chart: {
            title: 'Orders By Users',
            subtitle: 'Picked, Packed, and Shipped',
          }
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));
        // Convert the Classic options to Material options.
        function selectHandler() {
          var selectedItem = chart.getSelection()[0];
          if (selectedItem) {
            var topping = data.getValue(selectedItem.row, 0);
            $("#selected_date").val(topping);
            getModal();
          }
        }

        function getModal(){
            
            var form_cust = $('#GetOrderRecordsForModel')[0];
            let form1 = new FormData(form_cust);
            $.ajax({
                type:'POST',
                url: '{{ route('pipeline_and_metrix.GetTotalOrderModel') }}',
                data: form1,
                processData: false,
                contentType: false,
                success:function(response){
                    $("#orderChartModel").html('');
                    $('#orderChartModel').html(response);
                    $('#orderChartModel').modal('show');
                }
            })
        }

        google.visualization.events.addListener(chart, 'select', selectHandler); 
        chart.draw(data, google.charts.Bar.convertOptions(options));
      };

         
    </script>
    <div id="columnchart_material" style="width: 100%; height: 600px;"></div>

    <form id="GetOrderRecordsForModel">
        @if(isset($input))
            @foreach($input as $key_input => $row_input)
                <input type="hidden" name="{{$key_input}}" value="{{$row_input}}">
            @endforeach
        @endif
        <input type="hidden" name="selected_date" id="selected_date">
    </form>
    <div class="modal fade" id="orderChartModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
    