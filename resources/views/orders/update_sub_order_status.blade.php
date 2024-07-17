     
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Update Status</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="update_status_form" >
            <input type="hidden" name="order_id" value="{{$id}}">
            @csrf
            <div class="modal-body">
                <label for="status_id">Flag</label>
                <select class="form-control select2" name="status_id" id="status_id" >
                    @if($result)
                        @foreach($result as $row)
                            @php $add = 0; @endphp
                            @if($row->id <= $status)
                                @php $add = 1; @endphp
                            @endif
                            @if($status >= 9 && $row->id < 9)
                                @php $add = 0; @endphp
                            @endif
                            @if($add == 1)
                                <option value="{{$row->id}}" @if($row->id == $status) selected=selected @endif>{{ $row->status }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
            </div>  
                
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary .submit" >Update</button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </form>
    </div>
</div>

<script>
$("#update_status_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#update_status_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{route('orders.UpdateOrderDetailStatus')}}',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error == 0){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        location.reload();
                    },2000);
                }else{
                    $(".submit").attr("disabled", false);
                    toastr.error(response.msg);
                }
            },
            error: function(data){
                $(".submit").attr("disabled", false);
                var errors = data.responseJSON;
                $.each( errors.errors, function( key, value ) {
                    var ele = "#"+key;
                    $(ele).addClass('error');
                    $('<label class="error">'+ value +'</label>').insertAfter(ele);
                });
          }
        })
        return false;
    }
});
</script>