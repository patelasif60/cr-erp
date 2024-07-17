     
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Select Sub-Order For Order Number: {{ $order_number }} To Merge</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: black;">&times;</button>
        </div> 
        <div class="modal-body">
            <table class="table" id="sub_order_details_table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Sub Order Number</th>                                           
                    </tr>
                </thead>
                <tbody>
                    @if($result)
                        @foreach($result as $res)
                            <tr>
                                <td><input type="checkbox" name="mcb" value="{{ $res['sub_order_number'] }}"/></td>
                                <td>{{ $res['sub_order_number'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <button type="button" class="btn btn-primary float-right" onclick="mergeSubOrders()">Merge Sub-Orders</button>
        </div>
    </div>
</div>
