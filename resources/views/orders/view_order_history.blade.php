<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">     
<div class="modal-dialog mt-5 modal-lg" style="min-width: 50%">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header bg-light">
            <h5>View Order # {{$order_number}} History</h5>
            <!--Close/Cross Button-->
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div> 
        <div class="modal-body">
            <table class="table table-bordered text-center datatable" id="order_details">
                <thead class="thead-dark">
                    <tr>
                        <th>Order #</th>
                        <th>Sub Order #</th>
                        <th>Title</th>
                        <th>Detail</th>      
                        <th>User</th>                   
                        <th>TimeStamp</th>                         
                    </tr>
                </thead>
                <tbody>
                    @if($result)
                        @foreach($result as $row)
                            <tr>
                                <td>{{ $row->etailer_order_number }}</td>
                                <td>{{ $row->sub_order_number }}</td>
                                <td>{{ $row->action }}</td>
                                <td>{{ $row->details }}</td>
                                <td>{{ $row->user_name }}</td>
                                <td>{{ date('m/d/Y h:i:s A',strtotime($row->created_at)) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script>
    $('#order_details').DataTable({
        aaSorting: []
    });
</script>
