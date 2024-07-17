     
<div class="modal-dialog" id="edit_manufacturer">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Edit Brand</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="edit_brand_form" >
            <input name="_method" type="hidden" value="PATCH">
            <input type="hidden" name="id" value="{{ $row['id'] }}">
            @csrf
            <div class="modal-body">
                <lable for="brand">Brand Name</lable>
                <input type="text" class="form-control" name="brand" placesholder="Edit Product type" id="brand" style="width:100%;" value="{{ $row['brand'] }}"/> 
            </div> 
                
            <div class="modal-body">
                <lable for="manufacturer_id">Manufacturer</lable><br/>
                <select name="manufacturer_id" class="form-control" id="manufacturer_id" style="width:100%"> 
                    <option value="">Please Select</option>
                    @if($manufacturers)
                        @foreach($manufacturers as $key=>$value)
                            <?php $selected = '';?>
                            @if($key == $row['manufacturer_id'])
                                <?php $selected = 'selected';?>
                            @endif
                            <option value="{{$key}}" <?php echo $selected?>>{{$value}}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary" >Update</button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </form>
    </div>
</div>

<script>
$('#manufacturer_id').select2({
    dropdownParent: $("#edit_manufacturer")
});
$("#edit_brand_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#edit_brand_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{route('brands.update',$row['id'])}}',
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