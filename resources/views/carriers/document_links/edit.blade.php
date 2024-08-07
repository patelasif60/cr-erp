<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="javascript:void(0);" method="POST" id="editLinkForm">
            {{ csrf_field() }}
            <input type="hidden" name="carrier_id" id="carrier_id" value="{{ $row->carrier_id }}">
            <div class="row">
                <div class="col-md-8">
                    <label for="url">URl *</label>
                    <input type="text" name="url" id="url" class="form-control" value="{{ $row->url }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $row->name }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control">{{ $row->description }}</textarea>
                </div>
            </div>
            <div class="modal-footer mt-4">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
      </div>
    </div>
</div>

<script>
    $("#editLinkForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#editLinkForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('carriers.updateLink',$id) }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					GetLinks();
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
