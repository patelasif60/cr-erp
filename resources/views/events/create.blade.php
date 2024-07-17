<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="createEventModalLabel">Add Event</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="{{ route('clients.storeEvent') }}" method="POST" id="storeEventForm">
            @csrf
            <input type="hidden" name="client_id" id="client_id" value="{{ $id }}">
            <div class="row">
                <div class="col-md-8">
                    <label for="channel">Event Name *</label>
                    <input type="text" name="event" id="event" class="form-control required">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="frequency">Event Frequency</label>
                    <select name="frequency" id="frequency" class="form-control">
                        <option value="Daily">Daily</option>
                        <option value="Weekly">Weekly</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Yearly">Yearly</option>
                        <option value="Ad Hoc">Ad Hoc</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="day_and_time">Day & Time</label>
                    <input type="text" name="day_and_time" id="day_and_time" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="details">Details</label>
                    <input type="text" name="details" id="details" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="owner">Owner </label>
                    <select name="owner" id="owner" class="form-control select2_add">
                        <option value="">--Select--</option>
                        @if($users)
                            @foreach($users as $row_users)
                                <option value="{{ $row_users->id }}" >{{ $row_users->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    
                </div>
            </div>
            <div class="modal-footer mt-4">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
      </div>
    </div>
</div>

<!-- <script>
    $('#storeEventForm').validate();
</script> -->


<script>
    $('#day_and_time').flatpickr({
        static: true,
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });
    $('.select2_add').select2({
        dropdownParent: $('#createEventModal')
    });
    // $('#storeChannelForm').validate();

    	// $('input[name=weight]').attr('required', false);
$("#storeEventForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#storeEventForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('clients.storeEvent') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					eventList();
                    $('#createEventModal').modal('hide');
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
