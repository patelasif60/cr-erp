<div class="modal-dialog mt-5">
    <div class="modal-content">
        <div class="modal-header bg-light">
            <h5 class="modal-title" id="createEventModalLabel">Map the file</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="return_container">
            @if(empty($csvHeader))
                <form class="form-horizontal" method="POST" action="" enctype="multipart/form-data" id="form_upload">
                    {{ csrf_field() }}
                    <input type="hidden" name="client_id" value="{{ $client_id }}">
                    <div class="form-group">
                        <label for="csv_file" class="col-md-4 control-label">CSV file to map</label>
                        <div class="col-md-12">
                            <input id="csv_file" type="file" class="form-control" name="csv_file" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Map File
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <table class="table table-border">
                    <tr>
                        <th>3Pl Table Data</th>
                        <th>Your Data</th>
                    </tr>
                    @php $headers = json_decode($csvHeader->map_data) @endphp
                    @foreach($headers as $key => $row)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ $row }}</td>
                        </tr>
                    @endforeach
                </table>
                <a href="{{ route('clients.dalete_client_product_header',$csvHeader->id) }}" class="btn btn-danger" onClick="return confirm('are you sure?')">Delete Mapping</a>
            @endif
        </div>
    </div>
</div>


<script>
    // $('#storeChannelForm').validate();

    	// $('input[name=weight]').attr('required', false);
$("#form_upload").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
        $("#preloader").show();
		var form_cust = $('#form_upload')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('clients.MapClilentProduct') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
                    $("#preloader").hide();
					toastr.success(response.msg);
					$("#return_container").html(response.result);
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
