@extends('layouts.master')

@section('main-content')

<h3>Map Supplier DOT with Master Product Table</h3>

<form action="save_supplier_with_csv" method="POST" name="supplier_csv_form">
	@csrf

	<input type="hidden" name="supplier_name" value="{{ $supplier_name }}">
	<div class="row">
		<div class="col-md-8 col-md-offset-2" >
				<br>
				<div class="row">
					<div class="col-md-6"><b>Supplier Table</b></div>
					<div class="col-md-6"><b>Select CSV Field</b></div>
				</div>
				<br>
			@foreach($supplier_fields as $field)
				<div class="row">
					<div class="col-md-6"><span>{{$field}}</span>
					</div>
					<div class="col-md-6">
						<select name="{{$field}}">
							<option value="">Select</option>
							@foreach($header as $row)
							<option value="{{$row}}">{{$row}}</option>
							@endforeach
						</select>
					</div>
				</div>
			@endforeach
		</div>

	</div>
	<br><br>
	<button class="btn btn-primary" type="submit">Save</button>
</form>

@endsection