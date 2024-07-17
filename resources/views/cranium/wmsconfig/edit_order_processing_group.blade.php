     
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Edit Processing Group</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="edit_allergens_form" >
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <lable for="group_name">Group Name</lable>
                    <input type="text" name="group_name" class="form-control" placesholder="Group Name" id="group_name" style="width:100%;" value="{{ $row->group_name }}" readonly/> 
                </div>
                <div class="form-group">
                    <lable for="group_details">Temperature</lable>
                    <?php $selected_temp = explode(',',$row->group_details);?>
                    <select name="group_details[]" id="group_details" class="form-control select2" multiple>
                        @if($temp)
                            @foreach($temp as $key => $row_temp)
                            <option value="{{ $key }}" @if(in_array($key,$selected_temp)) selected=selected @endif>{{ $row_temp }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div> 
                
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary" >Update</button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </form>
    </div>
</div>

<script>
$("#edit_allergens_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#edit_allergens_form')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{ route('update_processing_group',$row->id) }}',
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

$('#group_details').multiselect({
    placeholder: 'Select Brands',
    search: true
});
</script>