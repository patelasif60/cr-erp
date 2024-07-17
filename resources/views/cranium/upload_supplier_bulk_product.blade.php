<div class="modal-dialog mt-5">
    <div class="modal-content">
        <div class="modal-header bg-light">
            <h5 class="modal-title" id="createEventModalLabel">Insert CSV Data in TABLES</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            @if(empty($csvHeader))
                <p class="p-3 bg-danger text-white">Please map your file first</p>
            @else
                @php $headers = json_decode($csvHeader->map_data) @endphp
                <form class="form-horizontal" method="POST" action="{{ route('suppliers.upload_supplier_product') }}" enctype="multipart/form-data" id="form_upload">
                {{ csrf_field() }}
                    <input type="hidden" name="supplier_name" value="{{$csvHeader->map_type}}" id="supplier_name">
                    <input type="hidden" name="supplier_id" value="{{ $supplier_id }}">
                    <input type="hidden" name="select_option" value="Upload & Edit">

                    <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">

                        <label for="csv_file" class="col-md-4 control-label">CSV file to import</label>
                        <div class="col-md-12">
                            
                            <!-- <select id="select_option" name="select_option" class="form-control mb-1">
                                <option value="Insert for Approval">Insert for Approval</option> 
                                <option value="Upload & Edit">Upload & Edit</option>
                            </select> -->
                            <select class="form-control mb-3" style="pointer-events:none" readonly>
                                <option value="">Select</option>
                                <option value="supplier_dot" @if($csvHeader->map_type == 'supplier_dot') selected @endif>DOT</option>
                                <option value="supplier_kehe" @if($csvHeader->map_type == 'supplier_kehe') selected @endif>KEHE</option>
                                <option value="supplier_dryers" @if($csvHeader->map_type == 'supplier_dryers') selected @endif>DRYERS</option>
                                <option value="supplier_hersley" @if($csvHeader->map_type == 'supplier_hersley') selected @endif>HERSLEY</option>
                                <option value="supplier_mars" @if($csvHeader->map_type == 'supplier_mars') selected @endif>MARS</option>
                                <option value="supplier_nestle" @if($csvHeader->map_type == 'supplier_nestle') selected @endif>NESTLE</option>
                                <option value="supplier_miscellaneous" @if($csvHeader->map_type == 'supplier_miscellaneous') selected @endif>MISC.</option>
                            </select>
                            <input id="csv_file" type="file" class="form-control" name="csv_file" required>
                            @if ($errors->has('csv_file'))
                                <span class="help-block">
                                <strong>{{ $errors->first('csv_file') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Insert Data
                            </button>
                        </div>
                    </div>
                </form>
                <table class="table table-border">
                    <tr>
                        <th>Table Data</th>
                        <th>Your Data</th>
                    </tr>
                    
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
			url: '{{ route('suppliers.upload_supplier_product') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
                    $("#preloader").hide();
					// toastr.success(response.msg);
					location.reload()
                    swal(
                        'Success',
                        response.msg,
                        'success'
                    ).then(function(){
                        // location.reload(true);
                    });
                    // $('#createEventModal').modal('hide');
				}else{
					$(".submit").attr("disabled", false);
					// toastr.error(response.msg);
                    $("#preloader").hide();
                    swal(
                        'Error',
                        response.msg,
                        'warning'
                    ).then(function(){
                        location.reload(true);
                    });
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
