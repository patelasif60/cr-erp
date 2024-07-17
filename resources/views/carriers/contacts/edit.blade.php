<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="exampleModalLabel">Edit Contact</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="javascript:void(0);" method="POST" id="editContactForm">
            {{ csrf_field() }}
            <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $row->supplier_id }}">
            <div class="row">
                <div class="col-md-8">
                    <label for="name">Contact Name *</label>
                    <input type="text" name="name" id="name" class="form-control required" value="{{ $row->name }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="title">Title</label>
                    <select name="title" id="title" class="form-control">
                        <option value="Manager" @if($row->title == "Manager") selected @endif>Manager</option>
                        <option value="Supervisor" @if($row->title == "Supervisor") selected @endif>Supervisor</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ $row->email }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="office_phone">Office Phone</label>
                    <input type="text" name="office_phone" id="office_phone" class="form-control" value="{{ $row->office_phone }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="cell_phone">Cell Phone </label>
                    <input type="text" name="cell_phone" id="cell_phone" class="form-control" value="{{ $row->cell_phone }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="contact_notes">Contact Notes </label>
                    <input type="text" name="contact_note" id="contact_note" class="form-control" value="{{ $row->contact_note }}">
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
    $("#editContactForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#editContactForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('suppliers.updateContact',$id) }}',
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
