<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="exampleModalLabel">Add New Contact</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="javascript:void(0);" method="POST" id="storeContactForm">
            @csrf
            <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $id }}">
            <div class="row">
                <div class="col-md-8">
                    <label for="name">Contact Name *</label>
                    <input type="text" name="name" id="name" class="form-control required">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="title">Title</label>
                    <select name="title" id="title" class="form-control">
                        <option value="Manager">Manager</option>
                        <option value="Supervisor">Supervisor</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="office_phone">Office Phone</label>
                    <input type="text" name="office_phone" id="office_phone" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="cell_phone">Cell Phone </label>
                    <input type="text" name="cell_phone" id="cell_phone" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="contact_notes">Contact Notes </label>
                    <input type="text" name="contact_notes" id="contact_notes" class="form-control">
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
    $("#storeContactForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#storeContactForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('suppliers.storeContact') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					contactList();
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
