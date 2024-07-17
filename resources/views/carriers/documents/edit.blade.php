<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="exampleModalLabel">Edit Document</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="{{ route('suppliers.updateChannel',$id) }}" method="POST" id="editDocumentForm"  enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $row->supplier_id }}">
            <div class="row">
                <div class="col-md-8">
                    <label for="channel">Channel Name *</label>
                    <input type="text" name="channel" id="channel" class="form-control required" value="{{ $row->channel }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="store_url">Store URl</label>
                    <input type="text" name="store_url" id="store_url" class="form-control" value="{{ $row->store_url }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="admin_url">Admin URL</label>
                    <input type="text" name="admin_url" id="admin_url" class="form-control" value="{{ $row->admin_url }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="username">Username</label>
                    <input type="email" name="username" id="username" class="form-control" value="{{ $row->username }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="password">Password </label>
                    <input type="password" name="password" id="password" class="form-control" value="{{ $row->channel }}">
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
    // $('#editChannelForm').validate();
    $("#editDocumentForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#editDocumentForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('suppliers.updateChannel',$id) }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					GetChanels();
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
