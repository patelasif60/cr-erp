<!-- <div class="modal-dialog mt-5">
    <div class="modal-content">
        <div class="modal-header bg-light">
            <h5 class="modal-title" id="createEventModalLabel">Map the file</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body"> -->
             <form id="save_mapping" class="form-horizontal" method="POST" action="{{ route('clients.saveClientImportHeaders') }}">
                {{ csrf_field() }}
               <input type="hidden" name="client_id" value="{{$client_id}}">
                <table class="col-md-11 table-striped text-center">
                    <thead>
                        <tr>
                            <th width="33%">Selection Description</th>
                            <th width="33%">Your Data Field</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (config('mapping.three_pl_client_field') as $db_field => $desc)
                        <tr>
                            <td>{{ $desc }}</td>
                            <td>
                                <select name="fields[{{ $db_field }}]" class="custom-select">
                                    <option value="Select">
                                        -
                                    </option>
                                    @foreach ($csv_data[0] as $key => $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>

                    <tr>

                    </tr>
                </table>
                <br>
                <br>
                <button type="submit" class="btn btn-secondary">
                    Save Map
                </button>

            </form>
        <!-- </div>
    </div>
</div> -->


<script>
    // $('#storeChannelForm').validate();

    	// $('input[name=weight]').attr('required', false);
$("#save_mapping").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
        $("#preloader").show();
		var form_cust = $('#save_mapping')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('clients.saveClientImportHeaders') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
                    $("#preloader").hide();
					toastr.success(response.msg);
					location.reload()
                    $('#createEventModal').modal('hide');
				}else{
					$(".submit").attr("disabled", false);
					toastr.error(response.msg);
                    $("#preloader").hide();
				}
			},
			error: function(data){
                $("#preloader").hide();
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

function change_supplier_name($this){
    if($($this).val() === 'Upload & Edit'){
        $("#supplier_name").val('master_product_queue');
    }else{
        $("#supplier_name").val('master_product');
    }
}

</script>
