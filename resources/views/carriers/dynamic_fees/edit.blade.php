<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="noteModalLabel">Edit Fuel Surcharge</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="javascript:void(0);" method="POST" id="editNoteForm">
            {{ csrf_field() }}
            <input type="hidden" name="carrier_id" id="carrier_id" value="{{ $row->carrier_id }}">
            <div class="row">
                <div class="col-md-8">
                    <label for="channel">Effective Date *</label>
                    <input type="text" name="effective_date" id="effective_date" value="{{ date('m/d/Y',strtotime($row->effective_date)) }}" class="form-control date-time required">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label for="details">Ground (%)*</label>
                    <input type="text" name="ground" id="ground" value="{{ $row->ground }}" class="form-control required">
                </div>
                <div class="col-md-8">
                    <label for="details">Domestic Air (%)*</label>
                    <input type="text" name="air" id="air" value="{{ $row->air }}" class="form-control required">
                </div>
                <div class="col-md-8">
                    <label for="international_air">International Air (%)*</label>
                    <input type="number" step="any" name="international_air" value="{{ $row->international_air }}" id="international_air" class="form-control required">
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
$(document).ready(function(){
    $('.date-time').flatpickr({
        static:true,
        enableTime: false,
        dateFormat: "m/d/Y",
    });
})

$("#editNoteForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#editNoteForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('carriers.updateFee',$row->id) }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					$('#exampleModal').modal('toggle');
                    carrierFeeList();
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

