<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="exampleModalLabel">Add Document</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="javascript:void(0);" method="POST" id="storeDocumentForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $id }}">
            <div class="row">
                <div class="col-md-8">
                    <label for="type">Document Type *</label>
                    <select name="type" id="type" class="form-control required">
                        <option value="contract">Contract</option>
                        <option value="product_uploads">Product Uploads</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="name">Document Name</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="password">Upload Document</label>
                    <input type="file" name="document" id="document" class="form-file-control required">
                </div>
            </div>
            <div class="modal-footer mt-4">
                <button type="submit" class="btn btn-primary submit">Save</button>
            </div>
          </form>
      </div>
    </div>
</div>

<script>

$("#storeDocumentForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#storeDocumentForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('suppliers.storeDocument') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					documentList();
                    $('#exampleModal').modal('hide');
				}else{
					$(".submit").attr("disabled", false);
					toastr.error(response.msg);
				}
			},
			error: function(data){
				$(".submit").attr("disabled", false);
				var errors = data.responseJSON;
				$("#error_container").html('');
				$.each( errors.errors, function( key, value ) {
					var ele = "#"+key;
					$(ele).addClass('error_border');
					$('<label class="error">'+ value +'</label>').insertAfter(ele);
					$("#error_container").append("<p class='bg-danger mb-1 text-white p-1'>"+ value +"</p>");
					toastr.error(value);
				});
			}
		})
		return false;
	}
});

</script>
