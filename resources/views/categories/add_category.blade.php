@php $required_span = '<span class="text-danger">*</span>' @endphp
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Add New Category</h3>
            <!--Close/Cross Button--> <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form action="#" method="POST" id="add_category">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $id }}">
            <input type="hidden" name="level" value="{{ $level }}">
            <div class="modal-body">
                <div class="form-group col-md-12">
                    <label for="name" class="ul-form__label">Product Category:<?php echo $required_span; ?></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Product Category">
                </div>
                <div class="form-group col-md-12">
                    <label for="sa_code" class="ul-form__label">SA Code:</label>
                    <input type="text" class="form-control" id="sa_code" name="sa_code" placeholder="Enter SA Code">
                </div>
            </div> 
                
            <div class="modal-footer"> 
                <button ty;e="submit" class="btn btn-primary" id="add-status">Add</button> 
                <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
            </div>
        </form>
    </div>
</div>
<script>
    $("#add_category").on('submit',function(e){
        e.preventDefault();
        $(".submit").attr("disabled", true);
        var form_cust = $('#add_category')[0]; 
        let form1 = new FormData(form_cust);
        $.ajax({
            type: "POST",
            url: '{{ route('categories.store') }}',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                $(".submit").attr("disabled", false);
                if(response.error == false){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        CategoryFromTopToBottom(response.data.id);
                        $("#GeneralModal").modal('hide');
                    },2000);
                }else{
                    toastr.error(response.msg);
                }
            },
            error: function(data){
                $(".submit").attr("disabled", false);
                $('.text-danger').remove();
                var errors = data.responseJSON;
                $.each( errors.errors, function( key, value ) {
                    var ele = "#"+key;
                    $(ele).addClass('border-danger');
                    $('<label class="text-danger">'+ value +'</label>').insertAfter(ele);
                });
            }
        })
        
    })
</script>