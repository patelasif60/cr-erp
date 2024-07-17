     
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Edit Unit Size</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="edit_manufacturer_form" >
            <input name="_method" type="hidden" value="PATCH">
            <input name="id" type="hidden" value="{{ $row['id']}}">
            @csrf
            <div class="modal-body">
                <label for="unit">Product Tags</label>
                <input type="text" class="form-control" name="unit" placesholder="Edit unit" id="unit" style="width:100%;" value="{{ $row['unit'] }}"/> 
            </div>
            <div class="modal-body">
                <label for="abbreviation">Abbreviation</label>
                <input type="text" class="form-control" name="abbreviation" placesholder="Edit Abbreviation" id="abbreviation" style="width:100%;" value="{{ $row['abbreviation'] }}"/> 
            </div>  
                
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary .submit" >Update</button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </form>
    </div>
</div>

<script>
$("#edit_manufacturer_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#edit_manufacturer_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{route('unit_sizes.update',$row['id'])}}',
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