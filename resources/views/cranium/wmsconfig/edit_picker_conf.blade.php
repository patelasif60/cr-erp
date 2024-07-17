     
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Edit Picker configration</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="edit_picker_form" >
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <lable for="order_processing_ids">Temperature</lable>
                    <select required name="order_processing_ids[]" id="order_processing_ids" class="form-control select2" multiple>
                        @if($orderProcess)
                            @foreach($orderProcess as $key => $row_temp)
                            <option value="{{$row_temp->id}}" @if(in_array($row_temp->id,$orderProcessing)) selected=selected @endif>{{ $row_temp->group_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="row">
                        <div class="col-12">
                            <lable for="batch_max_until_2pm">batch_max_until_2pm Type</lable>
                            <input required value = "{{$data?$data->batch_max_until_2pm:''}}" type="text" class="form-control" name="batch_max_until_2pm" id="batch_max_until_2pm" style="width:100%;"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <lable for="batch_max_2pm_to_4pm">batch_max_2pm_to_4pm</lable>
                            <input required value = "{{$data?$data->batch_max_2pm_to_4pm :''}}" type="text" class="form-control" name="batch_max_2pm_to_4pm" id="batch_max_2pm_to_4pm" style="width:100%;"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">                        
                            <lable for="batch_max_after_4pm">batch_max_after_4pm</lable>
                            <input required value = "{{$data?$data->batch_max_after_4pm:'' }}" type="text" class="form-control" name="batch_max_after_4pm"  id="batch_max_after_4pm" style="width:100%;"/>
                        </div>
                    </div>

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
$("#edit_picker_form").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#edit_picker_form')[0]; 
        let form1 = new FormData(form_cust);
        let wh_id = document.getElementById('pickwarehouses').selectedIndex;
        $.ajax({
            type: "POST",
            url: '{{ route('update_picker_conf',$id) }}',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                if(response.error == 0){
                    toastr.success(response.msg);
                    if (wh_id > 0) {
                        setTimeout(function() {
                            $('#MyModalPicker').modal('hide');
                            Getpicker(wh_id);
                        }, 2000);
                    } else {
                        setTimeout(function(){
                            location.reload();
                        },2000);
                    }
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