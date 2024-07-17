<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="noteModalLabel">Add Note</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="{{ route('suppliers.storeNote') }}" method="POST" id="storeNoteForm">
            @csrf
            <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $id }}">
            <div class="row">
                <div class="col-md-8">
                    <label for="channel">Event Name *</label>
                    <input type="text" name="event" id="event" class="form-control required">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="details">Details</label>
                    <textarea name="details" id="details" class="form-control"></textarea>
                </div>
            </div>
            {{--<div class="row">
                <div class="col-md-8">
                    <label for="user">User </label>
                    <select name="user" id="user" class="form-control">
                        <option value="">Select</option>
                        @if($users)
                            @foreach ($users as $id => $val)
                                <option value="{{ $id }}" @if(Auth::user()->id == $id) selected @endif>{{ $val }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>--}}
            {{-- <div class="row">
                <div class="col-md-8">
                    <label for="date_and_time">Date & Time</label>
                    <input type="text" name="date_and_time" id="date_and_time" class="form-control date-time">
                </div>
            </div> --}}
            <div class="modal-footer mt-4">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
      </div>
    </div>
</div>




<script>

$(document).ready(function(){
    $('.date-time').flatpickr({
        static:true,
        enableTime: true,
        dateFormat: "Y-m-d H:i",
    });
})

$("#storeNoteForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#storeNoteForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('suppliers.storeNote') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					noteList();
                    $('#exampleModal').modal('toggle');
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

