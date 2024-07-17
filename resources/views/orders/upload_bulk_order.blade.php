@extends('layouts.master')

@section('main-content')
<style>
	#loader {
		position: absolute;
		margin-left: 45%;
		margin-top: 10%;
	}
    #csv_file_import{
        padding: 3px;
    }

</style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<div class="container">
	<div class="col-md-8">
		<div class="panel panel-insert" id="panel-insert">
			<div class="panel panel-default card" style="padding: 20px;">
				<div class="panel-heading">
                    <h4>Bulk Order Upload</h4>
                </div>
				<div class="panel-body">
					<hr>
					<form class="form-horizontal" method="POST" action="#" enctype="multipart/form-data" id="form_upload">
						{{ csrf_field() }}
						<input type="hidden" name="client_name" id="client_name" value="{{ $client }}" />
						<div class="form-group">
							<div class="col-md-12 col-md-offset-4">
								<div class="" style="padding-left: 0 px;">
									<label>Select Client</label>
                                    <select id="client" name="client" class="form-control select2" required <?php if(isset($client)) echo 'disabled' ?>>
                                        <option value=""> --  Select Client --</option>
										@foreach ($clients as $cl)
											<option value="{{ $cl->id }}" <?php if(isset($client) && $client == $cl->id) echo 'selected'?>>{{ $cl->company_name }}</option>
										@endforeach                                                                                
                                    </select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12 col-md-offset-4">
								<div class="" style="padding-left: 0 px;">
									<label>Select Order Type</label>
                                    <select id="order_type" name="order_type" class="form-control select2" required>
                                        <option value=""> --  Select Order Type --</option>
										@foreach ($ots as $ot)
											<option value="{{ $ot->id }}">{{ $ot->name }}</option>
										@endforeach                                                                                
                                    </select>
								</div>
							</div>
						</div>
						<div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
							<label for="csv_file" class="col-md-4 control-label">File to import</label>

							<div class="col-md-12">
								<input id="csv_file_import" type="file" class="form-control" name="csv_file" required>
                                <span id="error_container"></span>
								@if ($errors->has('csv_file'))
									<span class="help-block">
									<strong>{{ $errors->first('csv_file') }}</strong>
								</span>
								@endif
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-12 col-md-offset-4">
								<button type="submit" class="btn btn-primary submit">
									Upload Data
								</button>
								<a href="{{ route('orders.index') }}" class="btn btn-danger">
									Cancel
								</a>
							</div>
						</div>
					</form>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">&nbsp;0%</div>
                    </div>

				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="MyModalOrderItm" data-backdrop="static">
	</div>
</div>

@endsection

@section('bottom-js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>


<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>


$("#form_upload").validate({
	submitHandler(form){	
		$(".submit").attr("disabled", true);
        var form_cust = $('#form_upload')[0];
		let form1 = new FormData(form_cust);
        $('div#myBar').removeClass('d-none');
        $('#preloader').show();
		$.ajax({
            beforeSubmit:function(){
                $('div#myBar').width('0%');
            },
            uploadProgress:function(event,progress,total,percentComplete){
                alert(percentComplete)
                $('div#myBar').width(percentComplete+'%');
            },
			type: "POST",
			url: '{{ route('orders.process_bulk_upload') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				$("#error_container").html('');
				$('#preloader').hide();
				if(response.error == 0){                    
					toastr.success(response.msg);
                    setTimeout(() => {
                        location.replace('{{ route('orders.index') }}');
                    }, 2000);
                    $('#createEventModal').modal('hide');
				}else{
					$(".submit").attr("disabled", false);
					if (response.type === 'validation') {
						showErrors(response.msg);
					} else {
						toastr.error(response.msg);					
					}
				}
			},
			error: function(data){
				$(".submit").attr("disabled", false);
				var errors = data.responseJSON;
				$("#error_container").html('');
				if (errors) {
					$.each( errors.errors, function( key, value ) {
						$('#preloader').hide();
						var ele = "#"+key;
						$(ele).addClass('error_border');
						$('<label class="error">'+ value +'</label>').insertAfter(ele);
						$("#error_container").append("<p class='bg-danger mb-1 mt-1 text-white p-1'>"+ value +"</p>");
						toastr.error(value);
					});
				}
				$("#error_container").html('');
				$('#preloader').hide();
				toastr.error('Error Occurred while uploading the file.');
			}
		})
		return false;
	}
});

function showErrors(errors) {	
	var form = new FormData();
	form.append('errors', errors);

	$.ajax({
		url: '{{route('orders.show_error')}}',
		method: 'POST',
		data: form,
		processData: false,
		contentType: false,
		success: function(res){
			$("#MyModalOrderItm").html(res);
			$("#MyModalOrderItm").modal();
		}
	});
}

</script>
@endsection
