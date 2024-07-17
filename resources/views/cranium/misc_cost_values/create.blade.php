<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="exampleModal">Add New</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="javascript:void(0);" method="POST" id="storeNoteForm">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <label for="data_point">Data Point:*</label>
                    <input type="text" name="data_point" id="data_point" class="form-control required">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="details">Details:</label>
                    <textarea type="text" name="details" id="details" class="form-control" rows="10"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="value">Value:*</label>
                    <input type="number" min="0.1" name="value" id="value" class="form-control required">
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

$("#storeNoteForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#storeNoteForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('misc_cost.store') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					location.reload()
                    $('#noteModal').modal('hide');
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

