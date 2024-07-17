@extends('layouts.master')

@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/custom/css/custom.css')}}">

@endsection
@section('main-content')

<div class="breadcrumb">
	<h1>Cranium</h1>
	<ul>
		<li><a href="/chanel_management">Channel Management</a></li>
		<li>Channel Products</li>
	</ul>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card o-hidden mb-4">
			<div class="card-header">
				<h3 class="w-50 float-left card-title m-0">Channel Products</h3>
			</div>
			<div class="card-body">
				<div class="table-responsive">
				<div class="form-group col-md-4">
					<label for="sales_manager" class="ul-form__label">Price Group:</label>
					<select class="form-control select2" onchange="GetNotApprovedProduct()" id="price_group" name="price_group">
						@if($priceGroup)
							@foreach($priceGroup as $key => $val)
								<option value="{{ $val->id }}">{{ $val->group_name }}</option>
							@endforeach
						@endif
					</select>
				</div>
					<table id="datatablenotapproved" class="table table-bordered text-center dataTable_filter">
						<thead>
							<tr>
								<th scope="col" id="idclass">#</th>
								<th scope="col">ETIN</th>
								<th scope="col">Product Listing Name</th>
								<th scope="col">Group Price</th>
								<th scope="col">Brand</th>
								<th scope="col">Supplier</th>
								<th scope="col">UPC</th>
								<th scope="col">Item Form Description</th>
								<th scope="col">Created By</th>
								<th scope="col">Updated By</th>
								<!-- <th scope="col">Action</th> -->
							</tr>
						</thead>
						
						<tbody>
						</tbody>
					</table>
					
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('page-js')
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
	<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>

<script type="text/javascript">
	$(function () {
		GetNotApprovedProduct();
	});

	//Not Approved Product List
	function GetNotApprovedProduct(){
		var table = $('#datatablenotapproved').DataTable({
            paging:   true,
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '{{ route('chanel_management.chanel_products',$id) }}',
                    method:'GET',
                    data: {
                        etin_filter2:$("#etin_filter2").val(),
                        listing_name_filter2:$("#listing_name_filter2").val(),
						brand_filter2:$("#brand_filter2").val(),
						manufacturer_filter2:$("#manufacturer_filter2").val(),
						unit_description_filter2:$("#unit_description_filter2").val(),
						supplier_filter2:$("#supplier_filter2").val(),
						product_filter2:$("#product_filter2").val(),
						upc_filter2:$("#upc_filter2").val(),
						item_form_desc_filter2:$("#item_form_desc_filter2").val(),
						price_group:$("#price_group").val()

                    }
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'ETIN', name: 'ETIN'},
				{data: 'product_listing_name', name: 'product_listing_name'},
				{data: 'group_price', name: 'group_price'},
                {data: 'brand', name: 'brand'},
                {data: 'current_supplier', name: 'current_supplier'},
                {data: 'upc', name: 'upc'},
				{data: 'item_form_description', name: 'item_form_description'},
				{data: 'username', name: 'username'},
				{data: 'users.name', name: 'users.name',"defaultContent": ""},
                // {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                },
				{
					orderable: false,
				 	targets: 1
				}
            ],

            oLanguage: {
                "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:"
            },
        });
	}


  </script>
@endsection
