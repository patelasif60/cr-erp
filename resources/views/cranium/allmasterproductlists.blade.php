@extends('layouts.master')

@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/custom/css/custom.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">


<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" /> -->

<!-- <style>

.bootstrap-select > .dropdown-toggle.bs-placeholder, .bootstrap-select > .dropdown-toggle.bs-placeholder:hover, .bootstrap-select > .dropdown-toggle.bs-placeholder:focus, .bootstrap-select > .dropdown-toggle.bs-placeholder:active {
    color: #000000;
}
</style> -->

@endsection
@section('main-content')
@if (session('approved'))
	<div class="alert alert-success" role="alert">
		{{ session('approved') }}
	</div>
@endif
@if (session('not_approved'))
	<div class="alert alert-danger" role="alert">
		{{ session('not_approved') }}
	</div>
@endif
<div class="breadcrumb">
	<h1>Cranium</h1>
	<ul>
		<li><a href="">All Master Product Listing</a></li>
		<li>Table View</li>
	</ul>
</div>
<div class="separator-breadcrumb border-top">
	@if(ReadWriteAccess('AddNewParentProduct'))
        <a href="{{ route('addnewmasterproductview')}}" class="btn btn-primary btn-icon m-1" style=" float: right;">
            <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New Parent Product
        </a>
    @endif
        <a href="{{ route('kits.create')}}" class="btn btn-primary btn-icon m-1" style=" float: right;">
            <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New Kit
        </a>
</div>
            <!-- end of row-->

<div class="row">
	<div class="col-md-12">
		<div class="card o-hidden mb-4">
			<div class="card-header">
				<h3 class="w-50 float-left card-title m-0">New Products Pending Approval</h3>

				<a onclick="refreshdatatable()"><img src="{{ asset('assets/images/refresh.png') }}" style="width: 25px; float: right; cursor: pointer;"></a>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table id="datatablenotapproved" class="table table-bordered text-center dataTable_filter">
						<thead>
							<tr>
								<!--<th scope="no">Category</th>-->
								<th scope="col" id="idclass">#</th>
								@if(Auth::user()->role != 3)
								<th><input type="checkbox" id="all_new_approve" name="all_new_approve"></th>
								@endif
								<th scope="col">ETIN</th>
								<th scope="col">Product Listing Name</th>
								<th scope="col">Brand</th>
								<th scope="col">Supplier</th>
								<th scope="col">UPC</th>
								<th scope="col">Item Form Description</th>
								<th scope="col">Created By</th>
								<th scope="col">Updated By</th>
								<th scope="col">Action</th>
							</tr>
						</thead>
						<thead>
							<tr>
								@if(Auth::user()->role != 3)
								<th scope="col"></th>
								@endif
								<th scope="col">
									<select name="etin_filter2[]" id="etin_filter2" class="form-control select2" multiple>
										<option value="">Select</option>
										@foreach ($getet2 as $row_etin2)
											<option value="{{ $row_etin2 }}">{{ $row_etin2 }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col">
									<select  id="listing_name_filter2" name="listing_name_filter2[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($listing_name2 as $ln)
											<option value="{{ $ln }}">{{ $ln }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col">
									<select  id="brand_filter2" name="brand_filter2[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($brand as $brandlist)
											<option value="{{ $brandlist }}">{{ $brandlist }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col">
									<select id="supplier_filter2" name="supplier_filter2[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($suppliers as $supplierslist)
											<option value="{{ $supplierslist }}">{{ $supplierslist }}</option>
										@endforeach
									</select>
								</th>

								<th scope="col">
									<select id="upc_filter2" name="upc_filter2[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($upcs2 as $upc2)
											<option value="{{ $upc2 }}">{{ $upc2 }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col">
									<select id="item_form_desc_filter2" name="item_form_desc_filter2[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($item_form_desc as $ifd)
											<option value="{{ $ifd }}">{{ $ifd }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col"></th>
								<th scope="col"></th>
								<th scope="col"></th>

							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					@if(Auth::user()->role != 3)
					<button id="newApprove" class="btn btn-primary ml-3">Approve Selected</button>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card o-hidden mb-4">
			<div class="card-header">
				<h3 class="w-50 float-left card-title m-0">Product Edits Pending Approval</h3>

				<a onclick="refreshdatatable()"><img src="{{ asset('assets/images/refresh.png') }}" style="width: 25px; float: right; cursor: pointer;"></a>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table id="datatableedited" class="table table-bordered text-center dataTable_filter">
						<thead>
							<tr>

								<th scope="col" id="idclass">#</th>
								@if(Auth::user()->role != 3)
								<th><input type="checkbox" id="all_edit_approve" name="all_edit_approve"></th>
								@endif
								<th scope="col">ETIN</th>
								<th scope="col">Product Listing Name</th>
								<th scope="col">Brand</th>
								<th scope="col">Supplier</th>
								<th scope="col">UPC</th>
								<th scope="col">Item Form Description</th>
								<th scope="col">Created By</th>
								<th scope="col">Updated By</th>
								<th scope="col">Action</th>
							</tr>
						</thead>
						<thead>
							<tr>
								@if(Auth::user()->role != 3)
								<th></th>
								@endif
								<th scope="col">
									<select name="etin_filter3[]" id="etin_filter3" class="form-control select2" multiple>
										<option value="">Select</option>
										@foreach ($getet3 as $row_etin3)
											<option value="{{ $row_etin3 }}">{{ $row_etin3 }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col">
									<select  id="listing_name_filter3" name="listing_name_filter3[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($listing_name3 as $ln)
											<option value="{{ $ln }}">{{ $ln }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col">
									<select  id="brand_filter3" name="brand_filter3[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($brand as $brandlist)
											<option value="{{ $brandlist }}">{{ $brandlist }}</option>
										@endforeach
									</select>
								</th>
								{{-- <th scope="col">
									<select id="manufacturer_filter3" name="manufacturer_filter3[]" class="form-control select2"  multiple>
										<option value=''>Select</option>
										@foreach($manufacturer as $manufacturerlist)
											<option value="{{ $manufacturerlist }}">{{ $manufacturerlist }}</option>
										@endforeach
									</select>
								</th> --}}
								<th scope="col">
									<select id="supplier_filter3" name="supplier_filter3[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($suppliers as $supplierslist)
											<option value="{{ $supplierslist }}">{{ $supplierslist }}</option>
										@endforeach
									</select>
								</th>
								{{-- <th scope="col">
									<select id="product_filter3" name="product_filter3[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($products as $product)
											<option value="{{ $product }}">{{ $product }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col">
									<select id="unit_description_filter3" name="unit_description_filter3[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($unitdesc as $unitdesclist)
											<option value="{{ $unitdesclist }}">{{ $unitdesclist }}</option>
										@endforeach
									</select>
								</th> --}}
								<th scope="col">
									<select id="upc_filter3" name="upc_filter3[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($upcs3 as $upc3)
											<option value="{{ $upc3 }}">{{ $upc3 }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col">
									<select id="item_form_desc_filter3" name="item_form_desc_filter3[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($item_form_desc as $ifd)
											<option value="{{ $ifd }}">{{ $ifd }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col"></th>
								<th scope="col"></th>
								<th scope="col"></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					@if(Auth::user()->role != 3)
					<button id="editApprove" class="btn btn-primary ml-3">Approve Selected</button>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="card o-hidden mb-4">
			<div class="card-header">
				<h3 class="w-50 float-left card-title m-0">Product Drafts</h3>

				<a onclick="refreshdatatable()"><img src="{{ asset('assets/images/refresh.png') }}" style="width: 25px; float: right; cursor: pointer;"></a>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table id="datatableadded" class="table table-bordered text-center dataTable_filter">
						<thead>
							<tr>

								<th scope="col" id="idclass">#</th>
								<th scope="col">ETIN</th>
								<th scope="col">Product Listing Name</th>
								<th scope="col">Brand</th>
								<th scope="col">Supplier</th>
								<th scope="col">UPC</th>
								<th scope="col">Item Form Description</th>
								<th scope="col">Created By</th>
								<th scope="col">Updated By</th>
								<th scope="col" width="30%">Action</th>
							</tr>

						</thead>
						<thead>
							<tr>
								<th scope="col">
									<select name="etin_filter4[]" id="etin_filter4" class="form-control select2" multiple>
										<!-- <option value="">Select</option> -->
										@foreach ($getet4 as $row_etin4)
											<option value="{{ $row_etin4 }}">{{ $row_etin4 }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col">
									<select  id="listing_name_filter4" name="listing_name_filter4[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($listing_name4 as $ln)
											<option value="{{ $ln }}">{{ $ln }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col">
									<select  id="brand_filter4" name="brand_filter4[]" class="form-control select2" multiple>
										<!-- <option value=''>Select</option> -->
										@foreach($brand as $brandlist)
											<option value="{{ $brandlist }}">{{ $brandlist }}</option>
										@endforeach
									</select>
								</th>

								<th scope="col">
									<select id="supplier_filter4" name="supplier_filter4[]" class="form-control select2" multiple>
										<!-- <option value=''>Select</option> -->
										@foreach($suppliers as $supplierslist)
											<option value="{{ $supplierslist }}">{{ $supplierslist }}</option>
										@endforeach
									</select>
								</th>

								<th scope="col">
									<select id="upc_filter4" name="upc_filter4[]" class="form-control select2" multiple>
										<!-- <option value=''>Select</option> -->
										@foreach($upcs4 as $upc4)
											<option value="{{ $upc4 }}">{{ $upc4 }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col">
									<select id="item_form_desc_filter4" name="item_form_desc_filter4[]" class="form-control select2" multiple>
										<option value=''>Select</option>
										@foreach($item_form_desc as $ifd)
											<option value="{{ $ifd }}">{{ $ifd }}</option>
										@endforeach
									</select>
								</th>
								<th scope="col"></th>
								<th scope="col"></th>
								<th scope="col"></th>

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
	<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script> -->
	<script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
	<script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
	<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
	<script src="{{asset('assets/js/es5/dashboard.v2.script.js')}}"></script>
	<script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
	<script src="{{asset('assets/js/sweetalert.script.js')}}"></script>

	<script type="text/javascript">
    // $(document).ready(function() {
    //     let branch_all = [];

    //     function formatResult(state) {
    //         if (!state.id) {
    //             var btn = $('<div class="text-right"><button id="all-branch" style="margin-right: 10px;" class="btn btn-default">Select All</button><button id="clear-branch" class="btn btn-default">Clear All</button></div>')
    //             return btn;
    //         }

    //         branch_all.push(state.id);
    //         var id = 'state' + state.id;
    //         var checkbox = $('<div class="checkbox"><input id="'+id+'" type="checkbox" '+(state.selected ? 'checked': '')+'><label for="checkbox1">'+state.text+'</label></div>', { id: id });
    //         return checkbox;
    //     }

    //     function arr_diff(a1, a2) {
    //         var a = [], diff = [];
    //         for (var i = 0; i < a1.length; i++) {
    //             a[a1[i]] = true;
    //         }
    //         for (var i = 0; i < a2.length; i++) {
    //             if (a[a2[i]]) {
    //                 delete a[a2[i]];
    //             } else {
    //                 a[a2[i]] = true;
    //             }
    //         }
    //         for (var k in a) {
    //             diff.push(k);
    //         }
    //         return diff;
    //     }

    //     let optionSelect2 = {
    //         templateResult: formatResult,
    //         closeOnSelect: false,
    //         width: '100%'
    //     };

    //     let $select2 = $("#country").select2(optionSelect2);

    //     var scrollTop;

    //     $select2.on("select2:selecting", function( event ){
    //         var $pr = $( '#'+event.params.args.data._resultId ).parent();
    //         scrollTop = $pr.prop('scrollTop');
    //     });

    //     $select2.on("select2:select", function( event ){
    //         $(window).scroll();

    //         var $pr = $( '#'+event.params.data._resultId ).parent();
    //         $pr.prop('scrollTop', scrollTop );

    //         $(this).val().map(function(index) {
    //             $("#state"+index).prop('checked', true);
    //         });
    //     });

    //     $select2.on("select2:unselecting", function ( event ) {
    //         var $pr = $( '#'+event.params.args.data._resultId ).parent();
    //         scrollTop = $pr.prop('scrollTop');
    //     });

    //     $select2.on("select2:unselect", function ( event ) {
    //         $(window).scroll();

    //         var $pr = $( '#'+event.params.data._resultId ).parent();
    //         $pr.prop('scrollTop', scrollTop );

    //         var branch  =   $(this).val() ? $(this).val() : [];
    //         var branch_diff = arr_diff(branch_all, branch);
    //         branch_diff.map(function(index) {
    //             $("#state"+index).prop('checked', false);
    //         });
    //     });

    //     $(document).on("click", "#all-branch",function(){
    //         $("#country > option").not(':first').prop("selected", true);// Select All Options
    //         $("#country").trigger("change")
    //         $(".select2-results__option").not(':first').attr("aria-selected", true);
    //         $("[id^=state]").prop("checked", true);
    //         $(window).scroll();
    //     });

    //     $(document).on("click", "#clear-branch", function(){
    //         $("#country > option").not(':first').prop("selected", false);
    //         $("#country").trigger("change");
    //         $(".select2-results__option").not(':first').attr("aria-selected", false);
    //         $("[id^=state]").prop("checked", false);
    //         $(window).scroll();
    //     });
    // });
</script>
<script type="text/javascript">
	$(function () {
		// GetActiveProducts();
		GetNotApprovedProduct();
		GetEditedProducts();
		GetDraftProducts();
		// GetProducts();
	});

	// //Filter's Onchange Event For Active Products
	// $("#etin_filter, #listing_name_filter, #brand_filter, #manufacturer_filter, #supplier_filter, #unit_description_filter, #product_filter, #upc_filter, #item_form_desc_filter").on('change',function(){
    //     GetActiveProducts();
    // });

	//Filter's Onchange Event For Not Approved Products
	$("#etin_filter2, #listing_name_filter2, #brand_filter2, #manufacturer_filter2, #supplier_filter2, #unit_description_filter2, #product_filter2, #upc_filter2, #item_form_desc_filter2").on('change',function(){
        GetNotApprovedProduct();
    });

	//Filter's Onchange Event For Edited Products
	$("#etin_filter3, #listing_name_filter3, #brand_filter3, #manufacturer_filter3, #supplier_filter3, #unit_description_filter3, #product_filter3, #upc_filter3, #item_form_desc_filter3").on('change',function(){
        GetEditedProducts();
    });

	//Filter's Onchange Event For Draft Products
	$("#etin_filter4, #listing_name_filter4, #brand_filter4, #manufacturer_filter4, #supplier_filter4, #unit_description_filter4, #product_filter4, #upc_filter4, #item_form_desc_filter4").on('change',function(){
        GetDraftProducts();
    });

	//Active Product List
	function GetActiveProducts(){
		var table = $('#datatable').DataTable({
            paging:   true,
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '{{ route('getmasterproducts') }}',
                    method:'GET',
                    data: {
                        etin_filter:$("#etin_filter").val(),
                        listing_name_filter:$("#listing_name_filter").val(),
						brand_filter:$("#brand_filter").val(),
						manufacturer_filter:$("#manufacturer_filter").val(),
						unit_description_filter:$("#unit_description_filter").val(),
						supplier_filter:$("#supplier_filter").val(),
						product_filter:$("#product_filter").val(),
						upc_filter:$("#upc_filter").val(),
						item_form_desc_filter:$("#item_form_desc_filter").val()
                    }
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'ETIN', name: 'ETIN'},
				{data: 'product_listing_name', name: 'product_listing_name'},
                {data: 'brand', name: 'brand'},
                {data: 'current_supplier', name: 'current_supplier'},
                {data: 'upc', name: 'upc'},
				{data: 'item_form_description', name: 'item_form_description'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                }
            ],
            oLanguage: {
                "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:"
            },
        });
	}

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
                    url: '{{ route('getnotapprovedmasterproducts') }}',
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
						item_form_desc_filter2:$("#item_form_desc_filter2").val()

                    }
                },
            columns: [
                {data: 'id', name: 'id'},
                @if(Auth::user()->role != 3)
                {data: 'approve_check', name: 'approve_check',  orderable: false},
				@endif
                {data: 'ETIN', name: 'ETIN'},
				{data: 'product_listing_name', name: 'product_listing_name'},
                {data: 'brand', name: 'brand'},
                {data: 'current_supplier', name: 'current_supplier'},
                {data: 'upc', name: 'upc'},
				{data: 'item_form_description', name: 'item_form_description'},
				{data: 'username', name: 'username'},
				{data: 'users.name', name: 'users.name',"defaultContent": ""},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                },
				{
					orderable: true,
				 	targets: 1
				}
            ],

            oLanguage: {
                "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:"
            },
        });
	}

	//Edited Product List
	function GetEditedProducts(){
		var table = $('#datatableedited').DataTable({
            paging: true,
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '{{ route('geteditedmasterproducts') }}',
                    method:'GET',
                    data: {
                        etin_filter3:$("#etin_filter3").val(),
                        listing_name_filter3:$("#listing_name_filter3").val(),
						brand_filter3:$("#brand_filter3").val(),
						manufacturer_filter3:$("#manufacturer_filter3").val(),
						unit_description_filter3:$("#unit_description_filter3").val(),
						supplier_filter3:$("#supplier_filter3").val(),
						product_filter3:$("#product_filter3").val(),
						upc_filter3:$("#upc_filter3").val(),
						item_form_desc_filter3:$("#item_form_desc_filter3").val()

                    }
                },
            columns: [
                {data: 'id', name: 'id'},
				@if(Auth::user()->role != 3)
                {data: 'approve_check', name: 'approve_check', orderable: false},
				@endif
                {data: 'ETIN', name: 'ETIN'},
				{data: 'product_listing_name', name: 'product_listing_name'},
                {data: 'brand', name: 'brand'},
                {data: 'current_supplier', name: 'current_supplier'},
                {data: 'upc', name: 'upc'},
                {data: 'item_form_description', name: 'item_form_description'},
				{data: 'inserted_by', name: 'inserted_by'},
				{data: 'username', name: 'username'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                },
				{
					orderable: true,
				 	targets: 1
				}
            ],
            oLanguage: {
                "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:"
            },
        });
	}

	//Draft Product List
	function GetDraftProducts(){
		var table = $('#datatableadded').DataTable({
            paging: true,
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '{{ route('getaddedmasterproducts') }}',
                    method:'GET',
                    data: {
                        etin_filter4:$("#etin_filter4").val(),
                        listing_name_filter4:$("#listing_name_filter4").val(),
						brand_filter4:$("#brand_filter4").val(),
						manufacturer_filter4:$("#manufacturer_filter4").val(),
						unit_description_filter4:$("#unit_description_filter4").val(),
						supplier_filter4:$("#supplier_filter4").val(),
						product_filter4:$("#product_filter4").val(),
						upc_filter4:$("#upc_filter4").val(),
						item_form_desc_filter4:$("#item_form_desc_filter4").val()

                    }
                },
            columns: [
                {data: 'id', name: 'ID'},
                {data: 'ETIN', name: 'ETIN'},
				{data: 'product_listing_name', name: 'product_listing_name'},
                {data: 'brand', name: 'brand'},
                {data: 'current_supplier', name: 'current_supplier'},
                {data: 'upc', name: 'upc'},
				{data: 'item_form_description', name: 'item_form_description'},
				{data: 'inserted_by', name: 'inserted_by'},
				{data: 'username', name: 'username'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                }
            ],
            oLanguage: {
                "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:"
            },
        });
	}

	//To Refresh All Datatables
	function refreshdatatable(){
		swal({
            title: 'Do you want to Refresh All the Master product tables?',
            text: "Reload all 3 Master tables",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0CC27E',
            cancelButtonColor: '#FF586B',
            confirmButtonText: 'Yes, Refresh!',
            cancelButtonText: 'No, Refresh not needed.',
            confirmButtonClass: 'btn btn-success mr-5',
            cancelButtonClass: 'btn btn-danger',
            buttonsStyling: false
        }).then(function () {
            if (confirm) {
				// GetActiveProducts();
				GetNotApprovedProduct();
				GetEditedProducts();
				GetDraftProducts();
            // GetProducts();
			}
		});
	}

	//Check-Uncheck All
	$('#all_new_approve').on('click',function(){
        if(this.checked){
            $('.newApproveCheckBox').each(function(){
                this.checked = true;
            });
        }else{
             $('.newApproveCheckBox').each(function(){
                this.checked = false;
            });
        }
    });

	//Check-Uncheck All
	$('#all_edit_approve').on('click',function(){
        if(this.checked){
            $('.editApproveCheckBox').each(function(){
                this.checked = true;
            });
        }else{
             $('.editApproveCheckBox').each(function(){
                this.checked = false;
            });
        }
    });

	//Mass Approve Selected New Products
	$('#newApprove').click(function(){
        var val = [];
        $('.newApproveCheckBox:checked').each(function(i){
          val[i] = $(this).val();
        });
		if(val.length !== 0){
			if(confirm("Are You Sure To Approve Selected Products ?")){
				$.ajax({
					type: "POST",
					url: '{{route('approveNewProducts')}}',
					data: {
						checked : val,
					},
					success: function( response ) {
						if(response.error == false){
							toastr.success(response.msg);
							setTimeout(function(){
								location.reload();
							},2000);
						}else{
							toastr.error(response.msg);
						}
					}
				});
			}
		}else{
			alert("Please Select Product To Approve");
		}
    });

	//Mass Approve Selected Edited Proucts
	$('#editApprove').click(function(){
        var val = [];
        $('.editApproveCheckBox:checked').each(function(i){
          val[i] = $(this).val();
        });
		if(val.length !== 0){
			if(confirm("Are You Sure To Approve Selected Products ?")){
				$.ajax({
					type: "POST",
					url: '{{route('approveEditProducts')}}',
					data: {
						checked : val,
					},
					success: function( response ) {
						if(response.error == false){
							toastr.success(response.msg);
							setTimeout(function(){
								location.reload();
							},2000);
						}else{
							toastr.error(response.msg);
						}
					}
				});
			}
		}else{
			alert("Please Select Product To Approve");
		}
    });

	function editeditedmasterproduct(){
		var table = null;
		var table = $('#datatableedited').DataTable({paging: true});
		$('#datatableedited tbody').on( 'click', 'tr', function () {
			var row = table.row( this ).data();
			window.location="editmasterrequestview/"+row['id'];
		});
	}

	function addedmasterproduct(){
		var table = null;
		var table = $('#datatableadded').DataTable({paging: true});
		$('#datatableadded tbody').on( 'click', 'tr', function () {
			var row = table.row( this ).data();
			window.location="editmasterrequestview/"+row['id'];
		});
	}

	// function GetProducts(){
    //     var table = $('#datatable').DataTable({
    //         destroy: true,
    //         responsive: true,
    //         processing: true,
    //         serverSide: true,
    //         autoWidth: false,
	// 		ajax:{
    //                 url: '{{ route('getmasterproducts') }}',
    //                 method:'GET',
    //                 data: {
    //                     etin_filter:$("#etin_filter").val(),
    //                     listing_name_filter:$("#listing_name_filter").val(),
	// 					brand_filter:$("#brand_filter").val(),
	// 					manufacturer_filter:$("#manufacturer_filter").val(),
	// 					unit_description_filter:$("#unit_description_filter").val(),
	// 					supplier_filter:$("#supplier_filter").val(),
	// 					product_filter:$("#product_filter").val(),
	// 					upc_filter:$("#upc_filter").val(),
	// 					item_form_desc_filter:$("#item_form_desc_filter").val()
    //                 }
    //             },
    //         columns: [
    //             {data: 'id', name: 'ID'},
    //             {data: 'ETIN', name: 'ETIN'},
	// 			{data: 'product_listing_name', name: 'product_listing_name'},
    //             {data: 'brand', name: 'brand'},
    //             {data: 'current_supplier', name: 'current_supplier'},
    //             {data: 'upc', name: 'upc'},
	// 			{data: 'item_form_description', name: 'item_form_description'},
    //             {data: 'action', name: 'Action', orderable: false},
    //         ],
    //         columnDefs: [
    //             {
    //                 "targets": [ 0 ],
    //                 "visible": false
    //             }
    //         ],
    //         oLanguage: {
    //             "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:"
    //         },
    //     });

	// 	$("#etin_filter2").on('change',function(){
    //     GetProducts();
	// 	});
	// 	$("#listing_name_filter2").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#brand_filter2").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#manufacturer_filter2").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#supplier_filter2").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#unit_description_filter2").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#product_filter2").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#upc_filter2").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#item_form_desc_filter2").on('change',function(){
    //     	GetProducts();
    // 	});

	// 	var table = $('#datatablenotapproved').DataTable({
    //         destroy: true,
    //         responsive: true,
    //         processing: true,
    //         serverSide: true,
    //         autoWidth: false,
	// 		ajax:{
    //                 url: '{{ route('getnotapprovedmasterproducts') }}',
    //                 method:'GET',
    //                 data: {
    //                     etin_filter2:$("#etin_filter2").val(),
    //                     listing_name_filter2:$("#listing_name_filter2").val(),
	// 					brand_filter2:$("#brand_filter2").val(),
	// 					manufacturer_filter2:$("#manufacturer_filter2").val(),
	// 					unit_description_filter2:$("#unit_description_filter2").val(),
	// 					supplier_filter2:$("#supplier_filter2").val(),
	// 					product_filter2:$("#product_filter2").val(),
	// 					upc_filter2:$("#upc_filter2").val(),
	// 					item_form_desc_filter2:$("#item_form_desc_filter2").val()

    //                 }
    //             },
    //         columns: [
    //             {data: 'id', name: 'ID'},
    //             @if(Auth::user()->role != 3)
    //             {data: 'approve_check', name: 'approve_check'},
	// 			@endif
    //             {data: 'ETIN', name: 'ETIN'},
	// 			{data: 'product_listing_name', name: 'product_listing_name'},
    //             {data: 'brand', name: 'brand'},
    //             {data: 'current_supplier', name: 'current_supplier'},
    //             {data: 'upc', name: 'upc'},
	// 			{data: 'item_form_description', name: 'item_form_description'},
	// 			{data: 'username', name: 'username'},
    //             {data: 'action', name: 'Action', orderable: false},
    //         ],
    //         columnDefs: [
    //             {
    //                 "targets": [ 0 ],
    //                 "visible": false
    //             },
	// 			{
	// 				orderable: false,
	// 			 	targets: 1
	// 			}
    //         ],

    //         oLanguage: {
    //             "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:"
    //         },
    //     });

	// 	$("#etin_filter3").on('change',function(){
    //     GetProducts();
	// 	});
	// 	$("#listing_name_filter3").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#brand_filter3").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#manufacturer_filter3").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#supplier_filter3").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#unit_description_filter3").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#product_filter3").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#upc_filter3").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#item_form_desc_filter3").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	var table = $('#datatableedited').DataTable({
    //         destroy: true,
    //         responsive: true,
    //         processing: true,
    //         serverSide: true,
    //         autoWidth: false,
	// 		ajax:{
    //                 url: '{{ route('geteditedmasterproducts') }}',
    //                 method:'GET',
    //                 data: {
    //                     etin_filter3:$("#etin_filter3").val(),
    //                     listing_name_filter3:$("#listing_name_filter3").val(),
	// 					brand_filter3:$("#brand_filter3").val(),
	// 					manufacturer_filter3:$("#manufacturer_filter3").val(),
	// 					unit_description_filter3:$("#unit_description_filter3").val(),
	// 					supplier_filter3:$("#supplier_filter3").val(),
	// 					product_filter3:$("#product_filter3").val(),
	// 					upc_filter3:$("#upc_filter3").val(),
	// 					item_form_desc_filter3:$("#item_form_desc_filter3").val()

    //                 }
    //             },
    //         columns: [
    //             {data: 'id', name: 'ID'},
	// 			@if(Auth::user()->role != 3)
    //             {data: 'approve_check', name: 'approve_check'},
	// 			@endif
    //             {data: 'ETIN', name: 'ETIN'},
	// 			{data: 'product_listing_name', name: 'product_listing_name'},
    //             {data: 'brand', name: 'brand'},
    //             {data: 'current_supplier', name: 'current_supplier'},
    //             {data: 'upc', name: 'upc'},
    //             {data: 'item_form_description', name: 'item_form_description'},
	// 			{data: 'username', name: 'username'},
    //             {data: 'action', name: 'Action', orderable: false},
    //         ],
    //         columnDefs: [
    //             {
    //                 "targets": [ 0 ],
    //                 "visible": false
    //             },
	// 			{
	// 				orderable: false,
	// 			 	targets: 1
	// 			}
    //         ],
    //         oLanguage: {
    //             "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:"
    //         },
    //     });

	// 	$("#etin_filter4").on('change',function(){
    //     GetProducts();
	// 	});
	// 	$("#listing_name_filter4").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#brand_filter4").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#manufacturer_filter4").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#supplier_filter4").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#unit_description_filter4").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#product_filter4").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#upc_filter4").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	$("#item_form_desc_filter4").on('change',function(){
	// 		GetProducts();
	// 	});
	// 	var table = $('#datatableadded').DataTable({
    //         destroy: true,
    //         responsive: true,
    //         processing: true,
    //         serverSide: true,
    //         autoWidth: false,
	// 		ajax:{
    //                 url: '{{ route('getaddedmasterproducts') }}',
    //                 method:'GET',
    //                 data: {
    //                     etin_filter4:$("#etin_filter4").val(),
    //                     listing_name_filter4:$("#listing_name_filter4").val(),
	// 					brand_filter4:$("#brand_filter4").val(),
	// 					manufacturer_filter4:$("#manufacturer_filter4").val(),
	// 					unit_description_filter4:$("#unit_description_filter4").val(),
	// 					supplier_filter4:$("#supplier_filter4").val(),
	// 					product_filter4:$("#product_filter4").val(),
	// 					upc_filter4:$("#upc_filter4").val(),
	// 					item_form_desc_filter4:$("#item_form_desc_filter4").val()

    //                 }
    //             },
    //         columns: [
    //             {data: 'id', name: 'ID'},
    //             {data: 'ETIN', name: 'ETIN'},
	// 			{data: 'product_listing_name', name: 'product_listing_name'},
    //             {data: 'brand', name: 'brand'},
    //             {data: 'current_supplier', name: 'current_supplier'},
    //             {data: 'upc', name: 'upc'},
	// 			{data: 'item_form_description', name: 'item_form_description'},
	// 			{data: 'username', name: 'username'},
    //             {data: 'action', name: 'Action', orderable: false},
    //         ],
    //         columnDefs: [
    //             {
    //                 "targets": [ 0 ],
    //                 "visible": false
    //             }
    //         ],
    //         oLanguage: {
    //             "sSearch": "Filter results Via ETIN, UPC, Manufacture, Category:"
    //         },
    //     });
    // }

  </script>
@endsection
