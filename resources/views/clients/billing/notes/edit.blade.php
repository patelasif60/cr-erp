<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="noteModalLabel">Edit Event</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="{{ route('clients.updateBillingNote',$id) }}" method="POST" id="editNoteForm">
            {{ csrf_field() }}
            <input type="hidden" name="client_id" id="client_id" value="{{ $row->client_id }}">
            <div class="row">
                <div class="col-md-8">
                    <label for="channel">Type *</label>
                    <select name="type" id="type" class="form-control" required>
                        @foreach($billingNotes as $billingNote)
                        <option @if($row->type == $billingNote->option) selected @endif  value="{{$billingNote->option}}">{{$billingNote->option}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="date">Task Date *</label>
                    <input name="date" id="date" class="form-control required" value="{{ $row->date }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="location">Location</label>
                    <select name="location" id="location" class="form-control">
                        <option value="">Select Location</option>
                        <option value="WI" @if($row->location == 'WI') selected @endif>WI</option>
                        <option value="PA" @if($row->location == 'PA') selected @endif>PA</option>
                        <option value="NV" @if($row->location == 'NV') selected @endif>NV</option>
                        <option value="OKC" @if($row->location == 'OKC') selected @endif>OKC</option>
                        <option value="Office" @if($row->location == 'Office') selected @endif>Office</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="details">Details</label>
                    <textarea type="text" name="details" id="details" class="form-control" rows="5">{{ $row->details }}</textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="document">Upload Document</label>
                    <input type="file" name="document" id="document" class="form-control">
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-8">
                    <label for="invoice_date">Invoice Date</label>
                    <input name="invoice_date" id="invoice_date"  value="{{$row->invoice_date}}" class="form-control required">
                </div>
            </div>
             <div class="row mt-4">
                <div class="col-md-8">
                   <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="is_billable" name="is_billable" value="1" {{ $row->is_billable ? 'checked': ' ' }}>
                      <label class="form-check-label" for="is_billable">Is Billable</label>
                    </div>
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

$(document).ready(function(){
    $('#date').flatpickr({
        static: true,
        enableTime: false,
        dateFormat: "Y-m-d",
    });
    $('#invoice_date').flatpickr({
        static: true,
        enableTime: false,
        dateFormat: "Y-m-d",
    });
})

$("#editNoteForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#editNoteForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('clients.updateBillingNote',$id) }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					billingNoteList();
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

