@php $required_span = '<span class="text-danger">*</span>' @endphp
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Add New Sub Category 2</h3>
            <!--Close/Cross Button--> <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form action="#" method="POST" id="add_category">
            <input type="hidden" name="product_category_id" value="{{ $row->product_category_id }}" >
            <input type="hidden" name="sub_category_1" value="{{ $row->sub_category_1 }}" >
            <input type="hidden" name="sc1_sa_code" value="{{ $row->sc1_sa_code }}" >    
            @csrf
            <div class="modal-body">
                <div class="form-group col-md-12">
                    <label for="sub_category_2" class="ul-form__label">Sub Category 2:<?php echo $required_span; ?></label>
                    <input type="text" class="form-control" id="sub_category_2" name="sub_category_2" placeholder="Enter Product Category 2">
                </div>
                <div class="form-group col-md-12">
                    <label for="sc2_sa_code" class="ul-form__label">SC2 SA Code:</label>
                    <input type="text" class="form-control" id="sc2_sa_code" name="sc2_sa_code" placeholder="Enter SC2 SA Code">
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
            url: '{{ route('categories.sub_category_2_store') }}',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                $(".submit").attr("disabled", false);
                if(response.error == false){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        GetCategoryHeirarchy('cat','{{ $row->product_category_id }}','sub_cat1','{{ $row->id }}');
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