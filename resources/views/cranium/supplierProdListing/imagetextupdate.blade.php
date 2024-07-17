     
<div class="modal-dialog" id="edit_manufacturer">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Edit</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="edit_brand_form" >
            {{-- <input name="_method" type="hidden" value="PATCH"> --}}
            <input type="hidden" name="id" value="{{ $row->id }}">
            <input type="hidden" name="ETIN" value="{{ $row->ETIN }}">
            @csrf
            <div class="modal-body">
                <lable for="image_text">Image Name</lable>
                <input type="text" class="form-control" name="image_text" placesholder="" id="image_text" style="width:100%;" value="{{ $row->image_text }}"/> 
            </div> 
                
            <div class="modal-body">
                <label for="" class="ul-form__label">Image Type</label>
                <select class="form-control image-type select2" name="image_type" id="image_type">
                    <option value="">Please Select</option>
                    @if($image_type)
                        @foreach($image_type as $image_types)
                            <option value="{{$image_types->image_type}}"  <?php if($image_types->image_type == $row->image_type ) echo "selected";?>>{{$image_types->image_type}}</option>
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
$('#image_type').select2({
    dropdownParent: $("#MyModal")
});
$("#edit_brand_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#edit_brand_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{route('imagetext_update',$row->id)}}',
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