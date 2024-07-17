     
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Edit Item Form Description</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="edit_item_desc_form" >
            <input name="_method" type="hidden" value="PATCH">
            <input type="hidden" name="id" value="{{ $row['id'] }}">
            @csrf
            <div class="modal-body">
                <lable for="item_desc">Item Form Description</lable>
                <input type="text" name="item_desc" class="form-control" id="item_desc" style="width:100%;" value="{{ $row['item_desc'] }}"/> 
            </div> 
                
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary" >Update</button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </form>
    </div>
</div>

<script>
$("#edit_item_desc_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#edit_item_desc_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{route('item_form_description.update',$row['id'])}}',
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