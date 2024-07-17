<div class="modal-dialog mt-5">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="noteModalLabel">Add Peak Surcharge</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="{{ route('carriers.storeSurcharge') }}" method="POST" id="storeNoteForm">
            @csrf
            <input type="hidden" name="carrier_id" id="carrier_id" value="{{ $carrier_id }}">
            <div class="row">
                {{-- <div class="col-md-8">
                    <label for="client_status" class="ul-form__label">Status:</label>
                    <select class="form-control" id="client_status" name="client_status" >
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Scheduled">Scheduled</option>
                    </select>
                </div> --}}
                <div class="col-md-8">
                    <label for="channel">Effective Date *</label>
                    <input type="text" name="effective_date" id="effective_date"  class="form-control date-time required">
                </div>
                <div class="col-md-8">
                    <label for="channel">End Date *</label>
                    <input type="text" name="end_date" id="end_date" class="form-control date-time">
                </div>
                <div class="col-md-8">
                    <label for="sure_post">Sure Post*</label>
                    <input type="number" step="any" name="sure_post" id="sure_post" class="form-control required">
                </div>
                <div class="col-md-8">
                    <label for="ground_residential">Ground Residential*</label>
                    <input type="number" step="any" name="ground_residential" id="ground_residential" class="form-control required">
                </div>
                <div class="col-md-8">
                    <label for="air_residential">Air Residential*</label>
                    <input type="number" step="any" name="air_residential" id="air_residential" class="form-control required">
                </div>
                <div class="col-md-8">
                    <label for="additional_handling">Additional Handling*</label>
                    <input type="number" step="any" name="additional_handling" id="additional_handling" class="form-control required">
                </div>
                <div class="col-md-8">
                    <label for="large_package_gt_50_lbs">Large Package > 50 lbs.*</label>
                    <input type="number" step="any" name="large_package_gt_50_lbs" id="large_package_gt_50_lbs" class="form-control required">
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

$("#storeNoteForm").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#storeNoteForm')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('carriers.storeSurcharge') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					surchargeList();
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

