<?php $required = '<span class="text-danger">*</span>'; ?>
<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="editEventModalLabel">Edit Link</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="{{ route('clients.updateLink',$id) }}" method="POST" id="editEventForm">
            {{ csrf_field() }}
            <input type="hidden" name="client_id" id="client_id" value="{{ $row->client_id }}">
            <div class="row">
                <div class="col-md-8">
                    <label for="url">URL:{!! $required !!}</label>
                    <input type="text" name="url" id="url" class="form-control" value="{{ $row->url }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="name">Name:{!! $required !!}</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $row->name }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="description">Description:</label>
                    <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{ $row->description }}</textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="date">Date:</label>
                    <input type="text" name="date" id="date" class="form-control" value="{{ $row->date }}">
                </div>
            </div>
            <div class="modal-footer mt-4">
                <button type="submit" class="btn btn-primary submit">Save</button>
            </div>
          </form>
      </div>
    </div>
</div>

<!-- <script>
    $('#editEventForm').validate();
</script> -->


<script>
    $('#date').flatpickr({
        static: true,
        enableTime: false,
        dateFormat: "Y-m-d",
    });

    $('.select2').select2({
        dropdownParent: $('#editEventModal')
    });
    // $('#editChannelForm').validate();
    $("#editEventForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#editEventForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('clients.updateLink',$id) }}',
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
