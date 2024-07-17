@extends('layouts.master')

@section('main-content')

<h3>Map Supplier DOT with Master Product Table</h3>

<form action="save_supplier_dot_with_master_product" method="POST" name="supplierDot_form">
	@csrf

	<input type="hidden" name="type" value="supplier_dot_with_master_product">
	<div class="row">
		<div class="col-md-8 col-md-offset-2" >
				<br>
				<div class="row">
					<div class="col-md-6"><b>DOT</b></div>
					<div class="col-md-6"><b>Master Product Table</b></div>
				</div>
				<br>
			@foreach($supplierDot as $field)
				<div class="row">
					<div class="col-md-6"><span>{{$field}}</span>
					</div>
					<div class="col-md-6">
						<select name="{{$field}}">
							<option value="">Select</option>
							@foreach($masterProduct as $field)
							<option value="{{$field}}">{{$field}}</option>
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