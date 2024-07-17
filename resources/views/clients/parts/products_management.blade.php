@extends('layouts.master')
@section('page-css')
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
    <style>
        .flatpickr-wrapper{
            width:100%;
        }
        .form-file-control {
            display: block;
            width: 100%;
            border: 1px solid #ced4da;
            padding: 0.375rem 0.75rem;
            font-size: .813rem;
            line-height: 1.5;
            color: #665c70;
            background-color: #fff;
            background-clip: padding-box;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .action{
            padding-top: 17px;
            display: inline-flex;
        }
    </style>
@endsection

@section('main-content')
    @php $required_span = '<span class="text-danger">*</span>' @endphp
    <div class="breadcrumb">
        <h1>Clients</h1>
        <ul>
            <li><a href="{{ route('clients.clients.orders') }}">Clients</a></li>
            <li>Product Management</li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card text-left">
                <div class="card-header bg-transparent">
                    <h6 class="card-title task-title">Product Listings</h6>
                    <div class="separator-breadcrumb" style="@if($row->business_relationship == 'Fulfillment') display:block @else display:none @endif">
                        @if(ReadWriteAccess('AddNewParentProduct'))
                            <a href="{{ route('addnewmasterproductview')}}?client_id={{ $row->id }}" class="btn btn-primary btn-icon m-1" style=" float: right;" target="_blank">
                                <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New Parent Product
                            </a>
                        @endif
                            <a href="{{ route('kits.create')}}?client_id={{ $row->id }}" class="btn btn-primary btn-icon m-1" style=" float: right;"  target="_blank">
                                <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Add New Kit
                            </a>
                            <a href="javascript:void(0);" onClick="getModal('{{ route('upload_bulk_product',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Bulk Upload
                            </a>
                            <a href="javascript:void(0);" onClick="getModal('{{ route('map_client_product_file',$row->id) }}')" class="btn btn-primary btn-icon m-1" style=" float: right;">
                                <img src="{{ asset('assets/images/addnew.png') }}" style="width: 15px; cursor: pointer;">&nbsp; Map File
                            </a>
                    </div>
                    <div class="dropdown dropleft text-right w-50 float-right">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8"></div>
                        <div class="col-lg-4">
                            
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered text-center dataTable_filter">

                            <thead>
                                <tr>
                                    <th scope="col">ETIN</th>
                                    <th scope="col">Product Listing Name</th>
                                    <th scope="col">Group Price</th>
                                    <th scope="col">Channels</th>
                                    <th scope="col">Inventory</th>
                                    <th scope="col">UPC</th>
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
    <!-- end of col -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true"></div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.script.js')}}"></script>
    <script>

       $(document).ready(function () {

            GetProducts();
       });
    function GetProducts(){
        var table = $('#datatable').DataTable({
            destroy: true,
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
			ajax:{
                    url: '{{ route('getmasterproductsbyclient',$row->id) }}',
                    method:'GET',
                    data: {
                        etin_filter:$("#etin_filter").val(),
                        listing_name_filter:$("#listing_name_filter").val(),
						unit_description_filter:$("#unit_description_filter").val(),
						product_filter:$("#product_filter").val(),
						upc_filter:$("#upc_filter").val(),
						status_filter:$("#status_filter").val(),
                        price_group : $('#price_group').val()
                    }
                },
            columns: [
                {data: 'ETIN', name: 'ETIN'},
				{data: 'product_listing_name', name: 'product_listing_name'},
                {data: 'group_price', name: 'group_price'},
                {data: 'channel', name: 'channel'},
                {data: 'inventory', name: 'inventory'},
                {data: 'upc', name: 'upc'},
				{data: 'status', name: 'status'},
                {data: 'action', name: 'Action', orderable: false},
            ],

            // columnDefs: [
            //     {
            //         "targets": [ 0 ],
            //         "visible": false
            //     }
            // ],
            oLanguage: {
                "sSearch": "Search:"
            },
        });
    }



    function getModal(url){
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                $("#exampleModal").html('');
                $('#exampleModal').html(response);
                $('#exampleModal').modal('show');
            }
        })
    }

    function editModal(url) {
        $.ajax({
            type:'GET',
            url:url,
            success:function(response){
                $("#editModal").html('');
                $('#editModal').html(response);
                $('#editModal').modal('show');
            }
        })
    }



   </script>
@endsection
