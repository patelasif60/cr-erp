     
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Warehouse Quantity</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        
            <div class="modal-body">
                <table class="table">
                    @if($result)
                        @foreach($result as $row)
                            <tr>
                                <td>{{$row['name']}}</td>
                                <td>{{$row['count']}}</td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div> 
                
            <div class="modal-footer"> 
                
            </div>
        
    </div>
</div>
