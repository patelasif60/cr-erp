     
<div class="modal-dialog mt-5 modal-lg" style="min-width: 50%">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header bg-light">
            <h5>Sub-Order For Order Number: <strong>{{ $order_number }}</strong></h5>
            <!--Close/Cross Button-->
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div> 
        <div class="modal-body">
            <table class="table table-bordered text-center" id="order_details">
                <thead class="thead-dark">
                    <tr>
                        <th>Sub Order Number</th>
                        <th>Fulfilled By</th>
                        <th>Warehouse</th>
                        <th>Carrier Service Type</th>
                        <th>Transit Time</th>
                        <th>Processing Groups</th> 
                        <th>Status</th>                                            
                        <th>Picker</th>                                            
                    </tr>
                </thead>
                <tbody>
                    @if($result)
                        @foreach($result as $res)
                            <tr>
                                <td>{{ $res['sub_order_number'] }}</td>
                                <td>{{ $res['fulfilled_by'] }}</td>
                                <td>{{ $res['warehouse'] }}</td>
                                <td>{{ $res['service_name'] }}</td>
                                <td>{{ $res['transit_time'] }}</td>
                                <td>{{ $res['pack_type'] }}</td>
                                <td>{{ $res['status'] }}</td>
                                <td>{{ $res['name'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
