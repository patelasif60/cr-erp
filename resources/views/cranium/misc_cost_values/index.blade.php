@extends('layouts.master')

@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/custom/css/custom.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">


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
		<li><a href="">Misc. Cost Values</a></li>
		<li>Table View</li>
	</ul>
</div>
<div class="separator-breadcrumb border-top">
    {{-- {{ route('addnewmisccost')}} --}}
</div>
            <!-- end of row-->

<div class="row">
	<div class="col-md-12">
		<div class="card o-hidden mb-4">
			<div class="card-header">
				<h3 class="w-50 float-left card-title m-0">Misc. Cost Values</h3>
				<a href="javascript:" onclick="getModal('{{ route('misc_cost.create') }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                    <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New
                </a>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table id="datatablenotapproved" class="table table-bordered text-center dataTable_filter">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">Data Point</th>
								<th scope="col">Details</th>
								<th scope="col">Value</th>
								<th scope="col">Action</th>
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
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
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

$(document).ready(function(){
    var table = $('#datatablenotapproved').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '{{ route('datatable.getmisccostvalues') }}',
                    method:'GET',
                },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'data_point', name: 'data_point'},
                {data: 'details', name: 'details'},
				{data: 'value', name: 'value'},
                {data: 'action', name: 'Action', orderable: false},
            ],
            columnDefs: [
                {
                    "targets": [ 0 ],
                    "visible": false
                }
            ],
            oLanguage: {
                "sSearch": "Search..:"
            },
        });
})


function getModal(url){
    $.ajax({
        type:'GET',
        url:url,
        success:function(response){
            $('#exampleModal').html(response);
            $('#exampleModal').modal('show');
        }
    })
}

function deleteCost(url){
    if(confirm('Are You Sure You Want To Delete This?')){
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                toastr.success("Success")
                location.reload();
            }
        })
    }
}
  </script>
@endsection
