     
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Edit Product Listing Filter</h3>
            <!--Close/Cross Button-->
             <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form  method="POST" action="javascript:void(0)" id="edit_product_listing_filter" >
            <input name="_method" type="hidden" value="PATCH">
            <input name="id" type="hidden" value="{{ $row->id}}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="label_name">Label Name</label>
                    <input type="text" class="form-control" name="label_name" id="label_name" value="{{ $row->label_name }}"/> 
                </div>
                <div class="form-group">
                    <label for="column_name">Column Name</label>
                    <input type="text" class="form-control" name="column_name" id="column_name" value="{{ $row->column_name }}"/> 
                </div>
                <div class="form-group">
                    <label for="text_or_select">Text/Label</label>
                    <select class="form-control" name="text_or_select" id="text_or_select" >
                        <option value="">--Select--</option>
                        <option value="Text" @if($row->text_or_select == "Text") selected @endif>Text</option>
                        <option value="Select" @if($row->text_or_select == "Select") selected @endif>Select</option>
                        <option value="custom_select"  @if($row->text_or_select == "custom_select") selected @endif>Custom Select</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="select_table">Select Table</label>
                    <input type="text" class="form-control" name="select_table" id="select_table" value="{{ $row->select_table }}"/> 
                </div>
                <div class="form-group">
                    <label for="select_value_column">Select Value Column</label>
                    <input type="text" class="form-control" name="select_value_column" id="select_value_column" value="{{ $row->select_value_column }}"/> 
                </div>
                <div class="form-group">
                    <label for="select_label_column">Select Label Column</label>
                    <input type="text" class="form-control" name="select_label_column" id="select_label_column" value="{{ $row->select_label_column }}"/> 
                </div>
                <div class="form-group">
                    <label for="custom_select">Custom Select Options</label>
                    <input type="text" class="form-control" name="custom_select_options" id="custom_select_options" value="{{ $row->custom_select_options }}"/> 
                </div>
                <div class="form-group">
                    <label for="is_default">Is Default ? </label>
                    <select class="form-control" name="is_default" id="is_default">
                        <option value="0" <?php echo $row->is_default == 0 ? 'selected' : ''?>>No</option>
                        <option value="1" <?php echo $row->is_default == 1 ? 'selected' : ''?>>Yes</option>
                    </select> 
                </div>
                <!-- <div class="form-group">
                    <label for="row_conditions">Row Conditions</label>
                    <textarea type="text" class="form-control" name="row_conditions" id="row_conditions">{{ $row->row_conditions }}</textarea> 
                </div>   -->
            </div>  
                
            <div class="modal-footer"> 
                <button type="submit" class="btn btn-primary .submit" >Update</button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </form>
    </div>
</div>

<script>
$("#edit_product_listing_filter").validate({
    submitHandler(form){
        $(".submit").attr("disabled", true);
        var form_cust = $('#edit_product_listing_filter')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{route('product_listing_filters.update',$row->id)}}',
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