@php $required_span = '<span class="text-danger">*</span>' @endphp
<div class="modal-dialog">
    <!--Modal Content-->
    <div class="modal-content">
        <!-- Modal Header-->
        <div class="modal-header" style="background-color:#fff;">
            <h3>Update New Sub Category 3</h3>
            <!--Close/Cross Button--> <button type="button" class="close reset-text" data-dismiss="modal" style="color: white;">&times;</button>
        </div> 
        <form action="#" method="POST" id="add_category">
            <input type="hidden" name="product_category_id" value="{{ $row->product_category_id }}" >
            <input type="hidden" name="sub_category_1" value="{{ $row->sub_category_1 }}" >
            <input type="hidden" name="sc1_sa_code" value="{{ $row->sc1_sa_code }}" >   
            <input type="hidden" name="sub_category_2" value="{{ $row->sub_category_2 }}" >
            <input type="hidden" name="sc2_sa_code" value="{{ $row->sc2_sa_code }}" >  
            <input type="hidden" name="id" value="{{ $row->id }}" >  
            <input type="hidden" name="delete" value="" id="delete" >  
            @csrf
            <div class="modal-body">
                <div class="form-group col-md-12">
                    <label for="sub_category_3" class="ul-form__label">Sub Category 3:<?php echo $required_span; ?></label>
                    <input type="text" class="form-control" id="sub_category_3" name="sub_category_3" placeholder="Enter Product Category 3" value="{{ $row->sub_category_3 }}">
                </div>
                <div class="form-group col-md-12">
                    <label for="sc3_sa_code" class="ul-form__label">SC3 SA Code:</label>
                    <input type="text" class="form-control" id="sc3_sa_code" name="sc3_sa_code" placeholder="Enter SC3 SA Code" value="{{ $row->sc3_sa_code }}">
                </div>
            </div> 
                
            <div class="modal-footer"> 
                <div class="form-group col-md-12">
                    <!-- <button type="submit" class="btn btn-primary" onClick="RemoveDelete()">Update</button>  -->
                    <!-- <button type="button" class="btn btn-danger" onClick="DeleteSubCat3()">Delete</button>  -->
                    <a href="" class="btn btn-outline-primary reset-text" data-dismiss="modal">Cancel</a> 
                </div>
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
            url: '{{ route('categories.sub_category_3_update') }}',
            data: form1, 
            processData: false,
            contentType: false,
            success: function( response ) {
                $(".submit").attr("disabled", false);
                if(response.error == false){
                    toastr.success(response.msg);
                    setTimeout(function(){
                        GetCategoryHeirarchy('cat',response.result.product_category_id,'sub_cat1',response.result.id,'sub_cat2',response.result.id);
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
        
    });

    function DeleteSubCat3()
    {
        if(confirm('Are you sure you want to delete this')){
            $("#delete").val(1);
            $("#add_category").submit();
        }
        
    }

    function RemoveDelete(){
        $("#delete").val(0);
    }
</script>