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
				<div class="panel-heading"><h2>Insert CSV Data in TABLES</h2></div>
				<div class="panel-body">
					<hr>
					<form class="form-horizontal" method="POST" action="{{ route('upload_csv_to_table') }}" enctype="multipart/form-data" id="form_upload">
						{{ csrf_field() }}
						<div class="form-group">
							<div class="col-md-12 col-md-offset-4">
								<div class="" style="padding-left: 0 px;">
									<label>Select Table to upload CSV Type</label>
                                    <select id="supplier_name_insert" name="supplier_name" class="form-control">
                                        <option value=""> --  Select Table Name to Upload CSV --</option>
                                        <option value="master_product">Master Product Table</option>
                                        <option value="sa_inventory">SA Inventory Table</option>
                                        <option value="dot_rlgl">DOT RLGL Table</option>
                                    </select>
								</div>
							</div>
						</div>


						<div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
							<label for="csv_file" class="col-md-4 control-label">CSV file to import</label>

							<div class="col-md-12">
								<input id="csv_file_import" type="file" class="form-control" name="csv_file" required>

								@if ($errors->has('csv_file'))
									<span class="help-block">
									<strong>{{ $errors->first('csv_file') }}</strong>
								</span>
								@endif
							</div>
						</div>

						<!--<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="header" checked> File contains header row?
									</label>
								</div>
							</div>
						</div>-->

						<div class="form-group">
							<div class="col-md-12 col-md-offset-4">
								<button type="submit" class="btn btn-primary">
									Insert Data
								</button>
							</div>
						</div>
					</form>

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
<script>
	$(document).ready(function() {
		$('#insert-or-update').on('change', function() {
		//alert( this.value );
			if(this.value == "insert"){
				$('#panel-insert').show();
				$('#panel-update').hide();
			}
			if(this.value == "update"){
				$('#panel-insert').hide();
				$('#panel-update').show();
			}
		});

	});

	function getmapdata(supplier){
		$("#datatable").html('');
		if(supplier == 'supplier_dot'){
			$.ajax({
                type:'GET',
                url:'{{ route('getdotmap') }}',
                data:$(this).serialize(),
				beforeSend: function(){
					// Show image container
						$("#loader").show();
					},
                success: function(data){
					$.each( data, function( key, value ) {
						var newrow = '<tr><td>' + key + '</td><td>' + value + '</td></tr>';
						$('#datatable').append(newrow);
					});
                },
				 complete:function(data){
					// Hide image container
						$("#loader").hide();
				}
            });
		} else if(supplier == 'supplier_kehe'){
			$.ajax({
                type:'GET',
                url:'{{ route('getkehemap') }}',
                data:$(this).serialize(),
				beforeSend: function(){
					// Show image container
						$("#loader").show();
					},
                success: function(data){
					$.each( data, function( key, value ) {
						var newrow = '<tr><td>' + key + '</td><td>' + value + '</td></tr>';
						$('#datatable').append(newrow);
					});
                },
				 complete:function(data){
					// Hide image container
						$("#loader").hide();
				}
            });
		} else if(supplier == 'supplier_mars'){
			$.ajax({
                type:'GET',
                url:'{{ route('getmarsmap') }}',
                data:$(this).serialize(),
				beforeSend: function(){
					// Show image container
						$("#loader").show();
					},
                success: function(data){
					$.each( data, function( key, value ) {
						var newrow = '<tr><td>' + key + '</td><td>' + value + '</td></tr>';
						$('#datatable').append(newrow);
					});
                },
				 complete:function(data){
					// Hide image container
						$("#loader").hide();
				}
            });
		} else if(supplier == 'supplier_dryers'){
			$.ajax({
                type:'GET',
                url:'{{ route('getdryerssmap') }}',
                data:$(this).serialize(),
				beforeSend: function(){
					// Show image container
						$("#loader").show();
					},
                success: function(data){
					$.each( data, function( key, value ) {
						var newrow = '<tr><td>' + key + '</td><td>' + value + '</td></tr>';
						$('#datatable').append(newrow);
					});
                },
				 complete:function(data){
					// Hide image container
						$("#loader").hide();
				}
            });
		} else if(supplier == 'supplier_hersley'){
			$.ajax({
                type:'GET',
                url:'{{ route('getharsleymap') }}',
                data:$(this).serialize(),
				beforeSend: function(){
					// Show image container
						$("#loader").show();
					},
                success: function(data){
					$.each( data, function( key, value ) {
						var newrow = '<tr><td>' + key + '</td><td>' + value + '</td></tr>';
						$('#datatable').append(newrow);
					});
                },
				 complete:function(data){
					// Hide image container
						$("#loader").hide();
				}
            });
		} else if(supplier == 'supplier_nestle'){
			$.ajax({
                type:'GET',
                url:'{{ route('getnestlemap') }}',
                data:$(this).serialize(),
				beforeSend: function(){
					// Show image container
						$("#loader").show();
					},
                success: function(data){
					$.each( data, function( key, value ) {
						var newrow = '<tr><td>' + key + '</td><td>' + value + '</td></tr>';
						$('#datatable').append(newrow);
					});
                },
				 complete:function(data){
					// Hide image container
						$("#loader").hide();
				}
            });
		} else if(supplier == 'supplier_miscellaneous'){
			$.ajax({
                type:'GET',
                url:'{{ route('getmiscmap') }}',
                data:$(this).serialize(),
				beforeSend: function(){
					// Show image container
						$("#loader").show();
					},
                success: function(data){
					if(data) {
					$.each( data, function( key, value ) {
						var newrow = '<tr><td>' + key + '</td><td>' + value + '</td></tr>';
						$('#datatable').append(newrow);
					});
					}
                },
				 complete:function(data){
					// Hide image container
						$("#loader").hide();
				}

            });
		}
	}

$('#supplier_name_insert').on('change', function() {
	//$("#datatable").empty();
	var supplier = $(this).val();
	$("#map-table-div").show();
	getmapdata(supplier);
});
$('#supplier_name_update').on('change', function() {
	//$("#datatable").empty();
	var supplier = $(this).val();
	$("#map-table-div").show();
	getmapdata(supplier);
});

$("#form_upload").validate({
	submitHandler(form){
		$(".submit").attr("disabled", true);
		var form_cust = $('#form_upload')[0];
		let form1 = new FormData(form_cust);
		$.ajax({
			type: "POST",
			url: '{{ route('upload_csv_to_table') }}',
			data: form1,
			processData: false,
			contentType: false,
			success: function( response ) {
				if(response.error == 0){
					toastr.success(response.msg);
					location.reload()
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
@endsection
