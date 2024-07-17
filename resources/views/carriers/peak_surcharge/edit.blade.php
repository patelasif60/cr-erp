<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="noteModalLabel">Edit Peak Surcharge</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="javascript:void(0);" method="POST" id="editNoteForm">
            {{ csrf_field() }}
            <input type="hidden" name="carrier_id" id="carrier_id" value="{{ $row->carrier_id }}">
            <div class="row">
                {{-- <div class="col-md-8">
                    <label for="client_status" class="ul-form__label">Status:</label>
                    <select class="form-control" id="client_status" name="client_status" >
                        <option value="Active" @if($row->status == "Active") selected @endif >Active</option>
                        <option value="Inactive" @if($row->status == "Inactive") selected @endif>Inactive</option>
                        <option value="Scheduled" @if($row->status == "Scheduled") selected @endif>Scheduled</option>
                    </select>
                </div> --}}
                <div class="col-md-8">
                    <label for="channel">Effective Date *</label>
                    <input type="text" name="effective_date" id="effective_date" value="{{ date('d/m/y',strtotime($row->effective_date)) }}" class="form-control date-time">
                </div>
                <div class="col-md-8">
                    <label for="channel">End Date *</label>
                    <input type="text" name="end_date" id="end_date" value="{{ date('d/m/y',strtotime($row->end_date)) }}" class="form-control date-time required">
                </div>
                <div class="col-md-8">
                    <label for="sure_post">Sure Post*</label>
                    <input type="text" step="any" name="sure_post" id="sure_post" value="{{ $row->sure_post }}" class="form-control required">
                </div>
                <div class="col-md-8">
                    <label for="ground_residential">Ground Residential*</label>
                    <input type="number" step="any" name="ground_residential" id="ground_residential" value="{{ $row->ground_residential }}" class="form-control required">
                </div>
                <div class="col-md-8">
                    <label for="air_residential">Air Residential*</label>
                    <input type="number" step="any" name="air_residential" id="air_residential" value="{{ $row->air_residential }}" class="form-control required">
                </div>
                <div class="col-md-8">
                    <label for="additional_handling">Additional Handling*</label>
                    <input type="number" step="any" name="additional_handling" id="additional_handling" value="{{ $row->additional_handling }}" class="form-control required">
                </div>
                <div class="col-md-8">
                    <label for="large_package_gt_50_lbs">Large Package > 50 lbs.*</label>
                    <input type="number" step="any" name="large_package_gt_50_lbs" id="large_package_gt_50_lbs" value="{{ $row->large_package_gt_50_lbs }}" class="form-control required">
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
        dateFormat: "d/m/y",
    });
})

$("#editNoteForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#editNoteForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('carriers.updateSurcharge',$row->id) }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					$('#exampleModal').modal('toggle');
                    surchargeList();
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

