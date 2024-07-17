     
<div class="modal-dialog mt-5 modal-lg" style="min-width: 50%">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header bg-light">
            <h5>View Tracking Details For Order # {{$order_number}}</h5>
            <!--Close/Cross Button-->
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div> 
        <div class="modal-body">
            <table class="table table-bordered text-center" id="order_details">
                <thead class="thead-dark">
                    <tr>
                        <th>Sub Order #</th>
                        <th>Package #</th>
                        <!-- <th>Status</th> -->
                        <th>Tracking #</th>                         
                    </tr>
                </thead>
                <tbody>
                    @if($result)
                        @foreach($result as $row)
                            <tr>
                                <td>{{ $row->order_id }}</td>
                                <td>{{ $row->package_num }}</td>
                                <!-- <td></td> -->
                                <td>{{ $row->tracking_number }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
