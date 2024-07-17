@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
@endsection

@section('main-content')
<div class="breadcrumb">
    <h1>Packaging & Materials</h1>
    <ul>
        <li><a href="javascript:void(0);"></a></li>
    </ul>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row mb-4">
    {{-- <div class="col-md-12 mb-4">
        <div class="card text-left">
            <div class="card-header bg-transparent">
                <h6 class="card-title task-title">List Of Packaging & Materials</h6>
            </div>
        </div>
    </div> --}}
    <div class="col-md-12">
        <div class="card o-hidden mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="packaging" class="table table-bordered text-center dataTable_filter">
                        <thead>
                            <tr>
                                <th scope="col">Supplier Name</th>
                                <th scope="col">ETIN</th>
                                <th scope="col">Product Description</th>
                                <th scope="col">Quantity Per Bundle</th>
                                <th scope="col">Product Temperature</th>
                                <th scope="col">Client Name</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        <!-- DATATABLE Here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
    <script>
    $(document).ready(function () {
        getSupplierPackaging();
    });
    getSupplierPackaging = () =>  {
        var table = $('#packaging').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                    url: '{{ route('getpackagingbysupplier') }}',
                    method:'GET',
                    data:{'type':'packaginglist'}
                },
            columns: [
                {data: 'supplier_name', name: 'supplier_name',defaultContent:'-'},
                {data: 'ETIN', name: 'ETIN',defaultContent:'-'},
                {data: 'product_description', name: 'product_description',defaultContent:'-'},
                {data: 'quantity_per_bundle', name: 'quantity_per_bundle',defaultContent:'-'},
                {data: 'product_temperature', name: 'product_temperature',defaultContent:'-'},
                {data:'client_name',name:'client_name',defaultContent:'-'},
                {data:'status', name: 'status',defaultContent:'-'},
                {data: 'action', name: 'action',searchable:false},
            ],
            columnDefs: [
                {
                    "targets": [],
                    "visible": false,
                    "data": "item_number",
                    "render": function ( data ) {
                        return '<a href="javascript:void(0)">Download</a>';
                    }
                }
            ],
            "searching":true
        });
    }
    deletePackagingMaterial = (id) => {
        swal({
            title: 'Are you sure?',
            text: "This information will be permanently deleted!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if(result) {
                $.ajax({
                    type: "Delete",
                    url: '{{ route('destroypackagematerial') }}',
                    data: {'id':id},
                    success: function( response ) {
                        $("#preloader").hide();
                        if(response.result){
                            toastr.success('Deleted Successfuly');
                            location.reload()
                        }else{
                            toastr.error("Already in use as component can't be delete");
                        }
                    }
                })
            }
        });
    }
    </script>
@endsection