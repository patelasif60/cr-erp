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
                    <h4>Insert CSV Data in Carrier Management & Configuration Tables</h4>
                </div>
				<div class="panel-body">
					<hr>
					<form class="form-horizontal" method="POST" action="{{ route('upload_ups_zone_rates_to_table') }}" enctype="multipart/form-data" id="form_upload">
						{{ csrf_field() }}
						<div class="form-group">
							<div class="col-md-12 col-md-offset-4">
								<div class="" style="padding-left: 0 px;">
									<label>Select Table to upload CSV Type</label>
                                    <select id="supplier_name_insert" name="table_name" class="form-control" required>
                                        <option value=""> --  Select Table Name to Upload CSV --</option>
                                        <option value="ups_zipzone_by_ground">UPS Zone Rates Ground</option>
                                        <option value="ups_zone_rates_air">UPS Zone Rates Air</option>
										<option value="ups_das_zip">UPS DAS Zip</option>
                                    </select>
								</div>
							</div>
						</div>
						<div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
							<label for="csv_file" class="col-md-4 control-label">CSV file to import</label>

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
									Insert Data
								</button>
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
			url: '{{ route('upload_ups_zone_rates_to_table') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
                    $("#error_container").html('');
                    $('#preloader').hide();
					toastr.success(response.msg);
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
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
                    $('#preloader').hide();
					var ele = "#"+key;
					$(ele).addClass('error_border');
					$('<label class="error">'+ value +'</label>').insertAfter(ele);
					$("#error_container").append("<p class='bg-danger mb-1 mt-1 text-white p-1'>"+ value +"</p>");
					toastr.error(value);
				});
			}
		})
		return false;
	}
});

var i = 0;
function move() {
  if (i == 0) {
    i = 1;
    var elem = document.getElementById("myBar");
    var width = 0;
    var id = setInterval(frame, 50);
    function frame() {
      if (width >= 100) {
        clearInterval(id);
        i = 0;
      } else {
        width++;
        elem.style.width = width + "%";
        elem.innerHTML = width + "%";
      }
    }
  }
}

</script>
@endsection

