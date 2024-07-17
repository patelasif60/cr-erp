
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawwarehouse);

      function drawwarehouse() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'Picked', 'Packed', 'Shipped'],
          <?php if (isset($chart_data)) echo $chart_data;?>
        ]);

        var options = {
          chart: {
            title: 'Orders By Warehouse',
            subtitle: 'Picked, Packed, and Shipped',
          }
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_warehouse'));
        // Convert the Classic options to Material options.
        function selectHandler() {
          var selectedItem = chart.getSelection()[0];
          if (selectedItem) {
            var topping = data.getValue(selectedItem.row, 0);
            $("#selected_wh").val(topping);
            getWHModal();
          }
        }

        function getWHModal(){
            
            var form_cust = $('#GetWHOrderRecordsForModel')[0];
            let form1 = new FormData(form_cust);
            $.ajax({
                type:'POST',
                url: '{{ route('pipeline_and_metrix.GetTotalOrderChartByWarehouseModel') }}',
                data: form1,
                processData: false,
                contentType: false,
                success:function(response){
                    $("#warehouseChartModel").html('');
                    $('#warehouseChartModel').html(response);
                    $('#warehouseChartModel').modal('show');
                }
            })
        }

        google.visualization.events.addListener(chart, 'select', selectHandler); 
        chart.draw(data, google.charts.Bar.convertOptions(options));
      };

         
    </script>
    <div id="columnchart_warehouse" style="width: 100%; height: 600px;"></div>

    <form id="GetWHOrderRecordsForModel">
        @if(isset($input))
            @foreach($input as $key_input => $row_input)
                <input type="hidden" name="{{$key_input}}" value="{{$row_input}}">
            @endforeach
        @endif
        <input type="hidden" name="selected_wh" id="selected_wh">
    </form>
    <div class="modal fade" id="warehouseChartModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
    